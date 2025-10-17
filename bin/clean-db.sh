#!/usr/bin/env bash
# Script to clean and reset PostgreSQL database

echo "==> Clearing all database tables..."

# Drop migrations table to reset state
php artisan tinker --execute="
    \DB::statement('DROP TABLE IF EXISTS migrations CASCADE');
    echo 'Migrations table dropped\n';
"

echo "==> Running migrations with --isolated flag..."
php artisan migrate --force --isolated

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force

echo "==> Database setup complete!"
echo ""
echo "Verifying tables..."
php artisan db:show
