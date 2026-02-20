<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to attendances table for improved range queries.
     * Note: UNIQUE(employee_id, work_date) already exists but adding non-unique indexes for range queries.
     * - Composite (employee_id, work_date): Optimized for period-based attendance queries
     * - work_date: Standalone filtering by date (reports, validations)
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Composite index for range queries filtering by employee + date period
            $table->index(['employee_id', 'work_date']);
            // Single index for date-based queries across all employees
            $table->index('work_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['employee_id', 'work_date']);
            $table->dropIndex(['work_date']);
        });
    }
};
