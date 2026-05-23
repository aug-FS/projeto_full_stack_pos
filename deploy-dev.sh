#!/usr/bin/env bash

set -Eeuo pipefail

APP_SERVICE="escola_app_php"
DB_CONTAINER="escola_app_db"

APP_NAME="Escola"
APP_URL="http://localhost"

DB_DATABASE="escola"
DB_USERNAME="root"
DB_PASSWORD="password"

ADMIN_EMAIL="admin@escola.com"
ADMIN_PASSWORD="admin1234"

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "==> Iniciando deploy DEV da Escola..."
echo "==> Projeto: ${PROJECT_DIR}"

cd "$PROJECT_DIR"

set_env() {
    local FILE="$1"
    local KEY="$2"
    local VALUE="$3"

    if grep -q "^${KEY}=" "$FILE"; then
        sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" "$FILE"
    else
        echo "${KEY}=${VALUE}" >> "$FILE"
    fi
}

ensure_gitignore_env() {
    echo "==> Garantindo .env fora do Git..."

    touch .gitignore

    if ! grep -qxF ".env" .gitignore; then
        echo ".env" >> .gitignore
    fi

    if ! grep -qxF ".env.*" .gitignore; then
        echo ".env.*" >> .gitignore
    fi

    if ! grep -qxF "!.env.example" .gitignore; then
        echo "!.env.example" >> .gitignore
    fi

    if ! grep -qxF "!.env.local" .gitignore; then
        echo "!.env.local" >> .gitignore
    fi

    if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
        if git ls-files --error-unmatch .env >/dev/null 2>&1; then
            echo ""
            echo "ERRO: .env ainda está rastreado pelo Git."
            echo "Rode:"
            echo "  git rm --cached .env"
            echo "  git add .gitignore"
            echo "  git commit -m \"Remove .env do git\""
            echo ""
            exit 1
        fi

        if [ -f .env.example ] && grep -q "^APP_KEY=base64:" .env.example; then
            echo ""
            echo "ERRO: .env.example contém APP_KEY real."
            echo "Deixe assim:"
            echo "  APP_KEY="
            echo ""
            exit 1
        fi
    fi

    echo "==> .env protegido. .env.local não será usado pelo deploy."
}

ensure_compose_uses_env_normal() {
    echo "==> Verificando docker-compose..."

    if grep -R "\.env\.local" docker-compose*.yml >/dev/null 2>&1; then
        echo ""
        echo "ERRO: docker-compose está usando .env.local."
        echo "Como o deploy deve usar somente .env, remova qualquer env_file: .env.local do docker-compose."
        echo ""
        grep -R "\.env\.local" docker-compose*.yml || true
        echo ""
        exit 1
    fi

    echo "==> docker-compose não referencia .env.local."
}

ensure_env_file() {
    echo "==> Garantindo arquivo .env..."

    if [ ! -f .env ]; then
        if [ -f .env.example ]; then
            cp .env.example .env
        else
            touch .env
        fi
    fi

    set_env ".env" APP_NAME "$APP_NAME"
    set_env ".env" APP_ENV "local"
    set_env ".env" APP_DEBUG "true"
    set_env ".env" APP_URL "$APP_URL"

    set_env ".env" DB_CONNECTION "mysql"
    set_env ".env" DB_HOST "mysql"
    set_env ".env" DB_PORT "3306"
    set_env ".env" DB_DATABASE "$DB_DATABASE"
    set_env ".env" DB_USERNAME "$DB_USERNAME"
    set_env ".env" DB_PASSWORD "$DB_PASSWORD"

    set_env ".env" SESSION_DRIVER "file"
    set_env ".env" CACHE_STORE "file"
    set_env ".env" QUEUE_CONNECTION "sync"

    set_env ".env" VITE_APP_NAME "$APP_NAME"

    echo "==> Garantindo APP_KEY somente no .env..."

    if ! grep -q "^APP_KEY=base64:" .env; then
        KEY=$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')

        if grep -q "^APP_KEY=" .env; then
            sed -i "s|^APP_KEY=.*|APP_KEY=${KEY}|" .env
        else
            echo "APP_KEY=${KEY}" >> .env
        fi
    fi

    if ! grep -q "^APP_KEY=base64:" .env; then
        echo "ERRO: APP_KEY não foi gerada corretamente no .env."
        grep "^APP_KEY=" .env || true
        exit 1
    fi

    echo "==> .env OK com APP_KEY."
}

prepare_landing_assets() {
    echo "==> Ajustando integração da landing com Vite..."

    mkdir -p resources/css resources/js

    touch resources/css/app.css
    touch resources/js/app.js

    if [ -f resources/css/landing.css ]; then
        if ! grep -q "@import './landing.css';" resources/css/app.css; then
            printf "\n@import './landing.css';\n" >> resources/css/app.css
        fi
    fi

    if [ -f resources/views/landing.blade.php ]; then
        sed -i "s|@vite(\['resources/css/landing.css'\])|@vite(['resources/css/app.css', 'resources/js/app.js'])|g" resources/views/landing.blade.php
        sed -i "s|@vite(\[\"resources/css/landing.css\"\])|@vite([\"resources/css/app.css\", \"resources/js/app.js\"])|g" resources/views/landing.blade.php
    fi
}

prepare_directories() {
    echo "==> Criando diretórios necessários..."

    mkdir -p \
        database/mysql \
        storage/framework/cache \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/app/livewire-tmp \
        storage/logs \
        bootstrap/cache \
        public/build

    touch storage/logs/laravel.log
}

fix_local_permissions() {
    echo "==> Ajustando permissões locais..."

    sudo chown -R "$USER:$USER" "$PROJECT_DIR"

    sudo chmod -R u+rwX,g+rwX "$PROJECT_DIR"

    sudo chmod -R 777 \
        "$PROJECT_DIR/storage" \
        "$PROJECT_DIR/bootstrap/cache" \
        "$PROJECT_DIR/database/mysql" \
        "$PROJECT_DIR/public/build"

    sudo chown -R 999:999 "$PROJECT_DIR/database/mysql" || true
    sudo chmod -R 777 "$PROJECT_DIR/database/mysql"
}

docker_down_up() {
    echo "==> Subindo containers..."

    docker compose down --remove-orphans
    docker compose build
    docker compose up -d
}

wait_mysql() {
    echo "==> Aguardando MySQL iniciar..."

    local MAX_TRIES=40
    local TRY=1

    until docker exec "$DB_CONTAINER" mysqladmin ping -h "127.0.0.1" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
        if [ "$TRY" -ge "$MAX_TRIES" ]; then
            echo "ERRO: MySQL não respondeu."
            echo "==> Logs do MySQL:"
            docker logs "$DB_CONTAINER" --tail=100 || true
            exit 1
        fi

        echo "Aguardando banco de dados... tentativa ${TRY}/${MAX_TRIES}"
        TRY=$((TRY + 1))
        sleep 3
    done

    echo "==> MySQL disponível."
}

install_dependencies() {
    echo "==> Instalando dependências PHP..."

    docker compose exec -T "$APP_SERVICE" composer install --no-interaction --prefer-dist

    echo "==> Instalando dependências Node..."

    if [ -f package-lock.json ]; then
        docker compose exec -T "$APP_SERVICE" npm ci
    else
        docker compose exec -T "$APP_SERVICE" npm install
    fi
}

prepare_laravel() {
    echo "==> Preparando Laravel dentro do container..."

    docker compose exec -T "$APP_SERVICE" sh -s <<'CONTAINER_SCRIPT'
set -e

mkdir -p \
    storage/framework/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/app/livewire-tmp \
    storage/logs \
    bootstrap/cache \
    public/build

touch storage/logs/laravel.log

chmod 1777 /tmp

echo "==> Ajustando permissões dentro do container..."

chown -R www-data:www-data storage bootstrap/cache public/build || true
chmod -R 777 storage bootstrap/cache public/build

echo "==> Removendo caches antigos do Laravel..."

rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

if [ ! -f .env ]; then
    echo "ERRO: .env não existe dentro do container."
    exit 1
fi

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "ERRO: APP_KEY não existe no .env dentro do container."
    grep "^APP_KEY=" .env || true
    exit 1
fi

echo "==> APP_KEY OK dentro do container."

echo "==> Limpando caches via Artisan..."

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan optimize:clear || true

php artisan storage:link || true

chown -R www-data:www-data storage bootstrap/cache public/build || true
chmod -R 777 storage bootstrap/cache public/build
CONTAINER_SCRIPT
}

restart_app_container() {
    echo "==> Reiniciando container da aplicação..."

    docker compose restart "$APP_SERVICE"

    sleep 3
}

build_assets() {
    echo "==> Gerando build do Vite..."

    docker compose exec -T "$APP_SERVICE" sh -lc '
        set -e

        echo "==> Removendo public/hot para evitar Vite dev server..."
        rm -f public/hot

        echo "==> Rodando npm run build..."
        npm run build

        echo "==> Validando manifest do Vite..."
        if [ ! -f public/build/manifest.json ]; then
            echo "ERRO: public/build/manifest.json não foi gerado."
            exit 1
        fi

        echo "==> Manifest gerado com sucesso."
        ls -la public/build

        chown -R www-data:www-data public/build || true
        chmod -R 777 public/build
    '
}

run_migrations() {
    echo "==> Rodando migrations..."

    docker compose exec -T "$APP_SERVICE" php artisan migrate --force
}

publish_filament_assets() {
    echo "==> Atualizando assets do Filament..."

    docker compose exec -T "$APP_SERVICE" php artisan filament:assets || true
    docker compose exec -T "$APP_SERVICE" php artisan optimize:clear || true
}

create_admin_user() {
    echo "==> Criando usuário admin DEV..."

    docker compose exec -T "$APP_SERVICE" php artisan tinker --execute="
\App\Models\User::updateOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name' => 'Admin Escola',
        'email_verified_at' => now(),
        'password' => bcrypt('$ADMIN_PASSWORD'),
    ]
);
"
}

clear_final_cache() {
    echo "==> Limpando cache final e garantindo modo build..."

    docker compose exec -T "$APP_SERVICE" sh -lc '
        rm -f public/hot

        php artisan config:clear || true
        php artisan cache:clear || true
        php artisan route:clear || true
        php artisan view:clear || true
        php artisan optimize:clear || true

        chown -R www-data:www-data storage bootstrap/cache public/build || true
        chmod -R 777 storage bootstrap/cache public/build
    '
}

finish_message() {
    echo ""
    echo "Deploy DEV finalizado com sucesso!"
    echo ""
    echo "Laravel:     http://localhost"
    echo "Filament:    http://localhost/admin"
    echo "phpMyAdmin:  http://localhost:8082"
    echo ""
    echo "Admin:"
    echo "Email:       $ADMIN_EMAIL"
    echo "Senha:       $ADMIN_PASSWORD"
    echo ""
    echo "Observação:"
    echo "Este deploy usa somente .env."
    echo ".env.local pode existir/subir, mas não é usado pelo deploy."
    echo "O arquivo public/hot é removido para carregar assets de public/build/manifest.json."
    echo ""
}

ensure_gitignore_env
ensure_compose_uses_env_normal
ensure_env_file
prepare_landing_assets
prepare_directories
fix_local_permissions
docker_down_up
wait_mysql
install_dependencies
prepare_laravel
restart_app_container
build_assets
run_migrations
publish_filament_assets
create_admin_user
clear_final_cache
fix_local_permissions
finish_message