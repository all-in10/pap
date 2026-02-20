<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to employees table for improved query performance.
     * - Composite (last_name, first_name): Accelerates employee name searches
     * - date_hired: Improves hiring date range queries and reports
     * - is_active: Fast filtering of active/inactive employees
     * - Composite (department_id, is_active): Department-based active employee listing
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['last_name', 'first_name']); // For name searches and sorting
            $table->index('date_hired'); // For hiring date filtering and reports
            $table->index('is_active'); // For active employee status filtering
            $table->index(['department_id', 'is_active']); // Composite: department + active status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['last_name', 'first_name']);
            $table->dropIndex(['date_hired']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['department_id', 'is_active']);
        });
    }
};
