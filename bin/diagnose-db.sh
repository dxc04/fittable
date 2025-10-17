#!/usr/bin/env bash
# Diagnostic script to check database state

echo "==> Checking database connection..."
php artisan db:show

echo ""
echo "==> Listing all tables..."
php artisan tinker --execute="
    \$tables = \DB::select('SELECT tablename FROM pg_tables WHERE schemaname = \'public\'');
    foreach (\$tables as \$table) {
        echo \$table->tablename . '\n';
    }
"

echo ""
echo "==> Checking if users table exists..."
php artisan tinker --execute="
    try {
        \$exists = \DB::select('SELECT to_regclass(\'public.users\')')[0]->to_regclass;
        if (\$exists) {
            echo 'Users table EXISTS\n';
            echo 'Describing users table:\n';
            \$columns = \DB::select('SELECT column_name, data_type FROM information_schema.columns WHERE table_name = \'users\'');
            foreach (\$columns as \$col) {
                echo '  ' . \$col->column_name . ' (' . \$col->data_type . ')\n';
            }
        } else {
            echo 'Users table does NOT exist\n';
        }
    } catch (Exception \$e) {
        echo 'Error: ' . \$e->getMessage() . '\n';
    }
"

echo ""
echo "==> Checking migrations table..."
php artisan tinker --execute="
    try {
        \$migrations = \DB::table('migrations')->get();
        echo 'Migrations run: ' . count(\$migrations) . '\n';
        foreach (\$migrations as \$migration) {
            echo '  ' . \$migration->migration . ' (batch ' . \$migration->batch . ')\n';
        }
    } catch (Exception \$e) {
        echo 'Error: ' . \$e->getMessage() . '\n';
    }
"
