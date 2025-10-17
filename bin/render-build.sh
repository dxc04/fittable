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

echo "==> Caching configuration..."
# Note: Database migrations are NOT run during build
# They must be run manually after deployment via bin/post-deploy.sh
# This is because the database is not accessible during the build phase
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Linking storage..."
php artisan storage:link || echo "Warning: Storage link failed."

echo "==> Build complete!"
