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
# Run migrations - if this fails, database might not be configured yet
php artisan migrate --force || echo "Warning: Migrations failed. You may need to run them manually after deployment."

echo "==> Seeding roles and permissions..."
# Seed roles - if this fails, database might not be configured yet
php artisan db:seed --class=RoleAndPermissionSeeder --force || echo "Warning: Seeding failed. You may need to seed manually after deployment."

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Linking storage..."
php artisan storage:link || echo "Warning: Storage link failed."

echo "==> Build complete!"
