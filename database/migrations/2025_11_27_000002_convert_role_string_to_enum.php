<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        try {
            $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'mysql') {
            // MySQL: modify the column to ENUM with allowed values
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('root','admin','hr','employee') NOT NULL DEFAULT 'employee'");
        } elseif ($driver === 'pgsql') {
            // Postgres: create a type if not exists, then alter column
            DB::statement(<<<'SQL'
DO $$ BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'user_role') THEN
        CREATE TYPE user_role AS ENUM ('root','admin','hr','employee');
    END IF;
END$$;
SQL
            );

            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE user_role USING role::user_role");
            DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'employee'::user_role");
        } else {
            // Fallback: try to alter to a VARCHAR that matches expected values (no-op for many drivers)
            // SQLite (used in tests) does not support ALTER COLUMN — skip the conversion to avoid test failures.
            if ($driver === 'sqlite' || $driver === 'sqlite3') {
                return;
            }
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE varchar(50)");
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        try {
            $driver = DB::getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        } catch (\Exception $e) {
            $driver = null;
        }

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(50) NOT NULL DEFAULT 'employee'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE varchar USING role::text");
            DB::statement("DROP TYPE IF EXISTS user_role");
        } else {
            // Best-effort fallback
            // SQLite (used in tests) does not support ALTER COLUMN — skip the conversion to avoid test failures.
            if ($driver === 'sqlite' || $driver === 'sqlite3') {
                return;
            }
            DB::statement("ALTER TABLE users ALTER COLUMN role TYPE varchar(50)");
        }
    }
};
