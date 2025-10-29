#!/bin/bash
set -e

echo "🚀 Starting deployment..."

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install NPM dependencies and build assets
echo "🎨 Building frontend assets..."
npm ci
npm run build

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed roles and permissions (only if needed)
echo "👤 Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force || echo "✓ Seeder already run"

# Clear and cache configuration
echo "🔧 Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment complete!"
