<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to contracts table for status and date range queries.
     * - Composite (status, employee_id): Fast "active contracts by employee" lookups
     * - Composite (start_date, end_date): Optimized for overlapping date validations
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Composite index for status filtering with employee context
            $table->index(['status', 'employee_id']);
            // Composite index for contract period validations and overlapping checks
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex(['status', 'employee_id']);
            $table->dropIndex(['start_date', 'end_date']);
        });
    }
};
