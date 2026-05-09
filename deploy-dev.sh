#!/usr/bin/env bash

set -e

APP_CONTAINER="escola_app_php"
DB_CONTAINER="escola_app_db"
ADMIN_EMAIL="admin@escola.com"
ADMIN_PASSWORD="admin1234"

echo "==> Iniciando deploy DEV da Escola..."

echo "==> Garantindo arquivo .env.local..."
if [ ! -f .env.local ]; then
    if [ -f .env ]; then
        cp .env .env.local
    else
        cp .env.example .env.local
    fi
fi

echo "==> Ajustando permissões locais..."
mkdir -p database/mysql storage bootstrap/cache
sudo chown -R "$USER:$USER" .
chmod -R 775 storage bootstrap/cache database/mysql

echo "==> Subindo containers..."
docker compose down --remove-orphans
docker compose build
docker compose up -d

echo "==> Aguardando MySQL iniciar..."
sleep 10

until docker exec "$DB_CONTAINER" mysqladmin ping -h "127.0.0.1" -uroot -ppassword --silent; do
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

echo "==> Rodando migrations..."
docker compose exec -T "$APP_CONTAINER" php artisan migrate --force

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
