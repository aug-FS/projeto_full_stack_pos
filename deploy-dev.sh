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

ensure_env_files() {
    echo "==> Garantindo arquivos .env e .env.local..."

    if [ ! -f .env ]; then
        cp .env.example .env
    fi

    if [ ! -f .env.local ]; then
        cp .env .env.local
    fi

    for FILE in .env .env.local; do
        set_env "$FILE" APP_NAME "$APP_NAME"
        set_env "$FILE" APP_ENV "local"
        set_env "$FILE" APP_DEBUG "true"
        set_env "$FILE" APP_URL "$APP_URL"

        set_env "$FILE" DB_CONNECTION "mysql"
        set_env "$FILE" DB_HOST "mysql"
        set_env "$FILE" DB_PORT "3306"
        set_env "$FILE" DB_DATABASE "$DB_DATABASE"
        set_env "$FILE" DB_USERNAME "$DB_USERNAME"
        set_env "$FILE" DB_PASSWORD "$DB_PASSWORD"

        set_env "$FILE" SESSION_DRIVER "file"
        set_env "$FILE" CACHE_STORE "file"
        set_env "$FILE" QUEUE_CONNECTION "sync"

        set_env "$FILE" VITE_APP_NAME "$APP_NAME"

        if ! grep -q "^APP_KEY=" "$FILE"; then
            echo "APP_KEY=" >> "$FILE"
        fi
    done
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
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/app/livewire-tmp \
        storage/logs \
        bootstrap/cache

    touch storage/logs/laravel.log
}

fix_local_permissions() {
    echo "==> Ajustando permissões locais..."

    sudo chown -R "$USER:$USER" "$PROJECT_DIR"

    sudo chmod -R u+rwX,g+rwX "$PROJECT_DIR"

    sudo chmod -R 777 \
        "$PROJECT_DIR/storage" \
        "$PROJECT_DIR/bootstrap/cache" \
        "$PROJECT_DIR/database/mysql"

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
    echo "==> Preparando Laravel, permissões, caches e APP_KEY..."

    docker compose exec -T "$APP_SERVICE" sh -s <<'CONTAINER_SCRIPT'
set -e

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/app/livewire-tmp \
    storage/logs \
    bootstrap/cache

touch storage/logs/laravel.log

chmod 1777 /tmp

echo "==> Ajustando permissões dentro do container..."

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 777 storage bootstrap/cache

echo "==> Removendo caches antigos do Laravel..."

rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/routes.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q "^APP_KEY=" .env; then
    echo "APP_KEY=" >> .env
fi

echo "==> Garantindo APP_KEY manualmente..."

if ! grep -q "^APP_KEY=base64:" .env; then
    KEY=$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')

    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${KEY}|" .env
    else
        echo "APP_KEY=${KEY}" >> .env
    fi
else
    KEY=$(grep "^APP_KEY=" .env | cut -d "=" -f2-)
fi

if [ -f .env.local ]; then
    if grep -q "^APP_KEY=" .env.local; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${KEY}|" .env.local
    else
        echo "APP_KEY=${KEY}" >> .env.local
    fi
fi

APP_KEY_VALUE=$(grep "^APP_KEY=" .env | cut -d "=" -f2-)

if [ -z "$APP_KEY_VALUE" ]; then
    echo "ERRO: APP_KEY está vazia no .env."
    exit 1
fi

echo "==> APP_KEY gravada no .env."

echo "==> Limpando caches via Artisan..."

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan optimize:clear || true

echo "==> Validando APP_KEY no arquivo .env..."

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "ERRO: APP_KEY não está no formato esperado."
    grep "^APP_KEY=" .env || true
    exit 1
fi

echo "==> APP_KEY OK."

php artisan storage:link || true

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 777 storage bootstrap/cache
CONTAINER_SCRIPT
}

build_assets() {
    echo "==> Gerando build do Vite..."

    docker compose exec -T "$APP_SERVICE" npm run build
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

start_vite_dev() {
    echo "==> Iniciando Vite em modo DEV..."

    docker compose exec -T "$APP_SERVICE" sh -lc '
        pkill -f "node.*vite" || true
        nohup npm run dev -- --host 0.0.0.0 > storage/logs/vite.log 2>&1 &
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
}

ensure_env_files
prepare_landing_assets
prepare_directories
fix_local_permissions
docker_down_up
wait_mysql
install_dependencies
prepare_laravel
build_assets
run_migrations
publish_filament_assets
create_admin_user
start_vite_dev
fix_local_permissions
finish_message