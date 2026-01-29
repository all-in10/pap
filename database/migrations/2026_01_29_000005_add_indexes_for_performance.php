<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('worklogs')) {
            Schema::table('worklogs', function (Blueprint $table) {
                $table->index(['employee_id', 'work_date'], 'idx_employee_workdate');
            });
        }

        if (Schema::hasTable('timeoffs')) {
            Schema::table('timeoffs', function (Blueprint $table) {
                $table->index(['employee_id', 'start_date', 'end_date'], 'idx_employee_timeoff_dates');
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->index('department_id', 'idx_employees_department');
            });
        }

        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->index(['employee_id', 'status'], 'idx_contracts_employee_status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('worklogs')) {
            Schema::table('worklogs', function (Blueprint $table) {
                $table->dropIndex('idx_employee_workdate');
            });
        }

        if (Schema::hasTable('timeoffs')) {
            Schema::table('timeoffs', function (Blueprint $table) {
                $table->dropIndex('idx_employee_timeoff_dates');
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropIndex('idx_employees_department');
            });
        }

        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropIndex('idx_contracts_employee_status');
            });
        }
    }
};