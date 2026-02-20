<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to benefits table for active benefits filtering.
     * - Composite (active, employee_id): Fast retrieval of active benefits per employee
     * - Composite (employee_id, start_date): Optimized for benefits eligibility in date ranges
     */
    public function up(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            // Composite index for "active benefits" queries (WHERE active = true AND employee_id = X)
            $table->index(['active', 'employee_id']);
            // Composite index for benefits in specific date ranges
            $table->index(['employee_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('benefits', function (Blueprint $table) {
            $table->dropIndex(['active', 'employee_id']);
            $table->dropIndex(['employee_id', 'start_date']);
        });
    }
};
