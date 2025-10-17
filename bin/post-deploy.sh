#!/usr/bin/env bash
# Post-deployment script to run after the service is deployed
# This should be run manually via Render Shell after first deployment

echo "==> Running post-deployment setup..."

echo "==> Checking database connection..."
php artisan db:show || {
    echo "ERROR: Cannot connect to database"
    echo "Please check your DB_URL environment variable"
    exit 1
}

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force

echo "==> Clearing caches..."
php artisan config:clear
php artisan cache:clear

echo "==> Caching configuration..."
php artisan config:cache

echo "==> Verifying tables..."
php artisan tinker --execute="
    echo 'Users table: ' . \DB::table('users')->count() . ' records\n';
    echo 'Cache table: ' . \DB::table('cache')->count() . ' records\n';
    echo 'Jobs table: ' . \DB::table('jobs')->count() . ' records\n';
    echo 'Sessions table: ' . \DB::table('sessions')->count() . ' records\n';
"

echo "==> Post-deployment setup complete!"
echo ""
echo "Your application is now ready to use."
