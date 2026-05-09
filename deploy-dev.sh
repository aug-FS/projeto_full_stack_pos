#!/usr/bin/env bash

set -e

APP_CONTAINER="escola_app_php"
DB_CONTAINER="escola_app_db"

APP_NAME="Escola"
APP_URL="http://localhost"

DB_DATABASE="escola"
DB_USERNAME="root"
DB_PASSWORD="password"

ADMIN_EMAIL="admin@escola.com"
ADMIN_PASSWORD="admin1234"

echo "==> Iniciando deploy DEV da Escola..."

set_env() {
    FILE=$1
    KEY=$2
    VALUE=$3

    if grep -q "^${KEY}=" "$FILE"; then
        sed -i "s|^${KEY}=.*|${KEY}=${VALUE}|" "$FILE"
    else
        echo "${KEY}=${VALUE}" >> "$FILE"
    fi
}

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
done

echo "==> Ajustando integração da landing com Vite..."

mkdir -p resources/css resources/js

if [ ! -f resources/css/app.css ]; then
    touch resources/css/app.css
fi

if [ ! -f resources/js/app.js ]; then
    touch resources/js/app.js
fi

if [ -f resources/css/landing.css ]; then
    if ! grep -q "@import './landing.css';" resources/css/app.css; then
        printf "\n@import './landing.css';\n" >> resources/css/app.css
    fi
fi

if [ -f resources/views/landing.blade.php ]; then
    sed -i "s|@vite(\['resources/css/landing.css'\])|@vite(['resources/css/app.css', 'resources/js/app.js'])|g" resources/views/landing.blade.php
    sed -i "s|@vite(\[\"resources/css/landing.css\"\])|@vite([\"resources/css/app.css\", \"resources/js/app.js\"])|g" resources/views/landing.blade.php
fi

echo "==> Ajustando permissões locais..."

mkdir -p \
    database/mysql \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/app/livewire-tmp \
    storage/logs \
    bootstrap/cache

sudo chown -R "$USER:$USER" .
chmod -R 775 storage bootstrap/cache

sudo chown -R 999:999 database/mysql
sudo chmod -R 775 database/mysql

echo "==> Subindo containers..."

docker compose down --remove-orphans
docker compose build
docker compose up -d

echo "==> Aguardando MySQL iniciar..."

sleep 10

until docker exec "$DB_CONTAINER" mysqladmin ping -h "127.0.0.1" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    echo "Aguardando banco de dados..."
    sleep 3
done

echo "==> Instalando dependências PHP..."

docker compose exec -T "$APP_CONTAINER" composer install --no-interaction --prefer-dist

echo "==> Instalando dependências Node..."

docker compose exec -T "$APP_CONTAINER" npm install

echo "==> Preparando Laravel..."

docker compose exec -T "$APP_CONTAINER" sh -lc '
    mkdir -p storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/app/livewire-tmp \
        storage/logs \
        bootstrap/cache

    chmod 1777 /tmp
    chown -R www-data:www-data storage bootstrap/cache
    chmod -R 775 storage bootstrap/cache

    if ! grep -q "^APP_KEY=base64:" .env; then
        php artisan key:generate --force
    fi

    php artisan storage:link || true
    php artisan optimize:clear
'

echo "==> Gerando build do Vite..."

docker compose exec -T "$APP_CONTAINER" npm run build

echo "==> Rodando migrations..."

docker compose exec -T "$APP_CONTAINER" php artisan migrate --force

echo "==> Atualizando assets do Filament..."

docker compose exec -T "$APP_CONTAINER" php artisan filament:assets || true
docker compose exec -T "$APP_CONTAINER" php artisan optimize:clear

echo "==> Criando usuário admin DEV..."

docker compose exec -T "$APP_CONTAINER" php artisan tinker --execute="
\App\Models\User::updateOrCreate(
    ['email' => '$ADMIN_EMAIL'],
    [
        'name' => 'Admin Escola',
        'email_verified_at' => now(),
        'password' => bcrypt('$ADMIN_PASSWORD'),
    ]
);
"

echo "==> Iniciando Vite em modo DEV..."

docker compose exec -T "$APP_CONTAINER" sh -lc '
    pkill -f "node.*vite" || true
    nohup npm run dev -- --host 0.0.0.0 > storage/logs/vite.log 2>&1 &
'

echo ""
echo "Deploy DEV finalizado com sucesso!"
echo ""
echo "Laravel:     http://localhost"
echo "Filament:    http://localhost/admin"
echo "phpMyAdmin:  http://localhost:8082"
echo ""
echo "Admin:"
echo "Email:       admin@escola.com"
echo "Senha:       admin1234"
echo ""
