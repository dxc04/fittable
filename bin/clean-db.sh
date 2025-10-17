#!/usr/bin/env bash
# Script to clean and reset PostgreSQL database

echo "==> Cleaning database..."

# Drop all tables manually using raw SQL
php artisan tinker --execute="
    try {
        \DB::statement('DROP TABLE IF EXISTS model_has_permissions CASCADE');
        \DB::statement('DROP TABLE IF EXISTS model_has_roles CASCADE');
        \DB::statement('DROP TABLE IF EXISTS role_has_permissions CASCADE');
        \DB::statement('DROP TABLE IF EXISTS permissions CASCADE');
        \DB::statement('DROP TABLE IF EXISTS roles CASCADE');
        \DB::statement('DROP TABLE IF EXISTS assessments CASCADE');
        \DB::statement('DROP TABLE IF EXISTS job_analyses CASCADE');
        \DB::statement('DROP TABLE IF EXISTS resumes CASCADE');
        \DB::statement('DROP TABLE IF EXISTS job_postings CASCADE');
        \DB::statement('DROP TABLE IF EXISTS failed_jobs CASCADE');
        \DB::statement('DROP TABLE IF EXISTS job_batches CASCADE');
        \DB::statement('DROP TABLE IF EXISTS jobs CASCADE');
        \DB::statement('DROP TABLE IF EXISTS cache_locks CASCADE');
        \DB::statement('DROP TABLE IF EXISTS cache CASCADE');
        \DB::statement('DROP TABLE IF EXISTS sessions CASCADE');
        \DB::statement('DROP TABLE IF EXISTS password_reset_tokens CASCADE');
        \DB::statement('DROP TABLE IF EXISTS users CASCADE');
        \DB::statement('DROP TABLE IF EXISTS migrations CASCADE');
        echo 'All tables dropped successfully\n';
    } catch (Exception \$e) {
        echo 'Error: ' . \$e->getMessage() . '\n';
    }
"

echo "==> Running fresh migrations..."
php artisan migrate --force

echo "==> Seeding roles and permissions..."
php artisan db:seed --class=RoleAndPermissionSeeder --force

echo "==> Database cleanup and migration complete!"
