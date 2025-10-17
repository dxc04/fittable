#!/usr/bin/env bash
# Script to clean and reset PostgreSQL database

echo "==> Terminating other database connections..."
php artisan tinker --execute="
    try {
        // Get current connection PID
        \$currentPid = \DB::selectOne('SELECT pg_backend_pid() as pid')->pid;
        echo 'Current PID: ' . \$currentPid . '\n';

        // Terminate all other connections to this database
        \$dbName = config('database.connections.pgsql.database');
        \DB::statement(\"
            SELECT pg_terminate_backend(pg_stat_activity.pid)
            FROM pg_stat_activity
            WHERE pg_stat_activity.datname = ?
            AND pid <> ?
        \", [\$dbName, \$currentPid]);

        echo 'Other connections terminated\n';
    } catch (Exception \$e) {
        echo 'Note: ' . \$e->getMessage() . '\n';
    }
"

echo "==> Dropping all tables..."
php artisan tinker --execute="
    \DB::statement('DROP SCHEMA public CASCADE');
    \DB::statement('CREATE SCHEMA public');
    \DB::statement('GRANT ALL ON SCHEMA public TO public');
    echo 'Schema reset complete\n';
"

echo "==> Running fresh migrations..."
php artisan migrate --force

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force

echo "==> Database setup complete!"
echo ""
echo "Verifying tables..."
php artisan db:show
