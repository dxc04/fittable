#!/usr/bin/env bash
# exit on error
set -o errexit

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm ci
npm run build

# Generate application key if not set
php artisan key:generate --force

# Run database migrations
php artisan migrate --force

# Seed roles and permissions
php artisan db:seed --class=RoleAndPermissionSeeder --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link
