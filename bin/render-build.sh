#!/usr/bin/env bash
# exit on error
set -o errexit

echo "==> Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "==> Installing Node dependencies and building assets..."
npm ci
npm run build

echo "==> Generating application key..."
php artisan key:generate --force

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Linking storage..."
php artisan storage:link

echo "==> Build complete!"
