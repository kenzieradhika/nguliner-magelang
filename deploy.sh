#!/usr/bin/env bash
# Deploy script NGuliner Magelang (Laravel)
# Pemakaian: bash deploy.sh [production]

set -e

MODE="${1:-production}"

echo "==> 1/6 Install dependencies composer"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> 2/6 Build asset frontend"
npm install
npm run build

echo "==> 3/6 Konfigurasi env"
if [ ! -f .env ]; then
    cp .env.production.example .env
    echo "   .env dibuat dari .env.production.example — isi APP_KEY & DB_DATABASE dulu"
    php artisan key:generate
fi

echo "==> 4/6 Storage link"
php artisan storage:link

echo "==> 5/6 Migrasi database"
php artisan migrate --force

echo "==> 6/6 Optimasi & cache"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Selesai. Jangan lupa jadwalkan scheduler:"
echo "  * * * * * php /path/ke/nguliner/artisan schedule:run >> /dev/null 2>&1"
