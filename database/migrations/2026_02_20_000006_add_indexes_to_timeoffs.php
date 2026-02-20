<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds critical indexes to timeoffs table for date overlap detection and status filtering.
     * - Composite (employee_id, start_date, end_date): Optimized for conflict detection O(log N)
     * - Composite (status, employee_id): Fast filtering of pending/approved timeoffs per employee
     */
    public function up(): void
    {
        Schema::table('timeoffs', function (Blueprint $table) {
            // Composite index for date overlap validation and period queries
            $table->index(['employee_id', 'start_date', 'end_date']);
            // Composite index for status-based filtering
            $table->index(['status', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timeoffs', function (Blueprint $table) {
            $table->dropIndex(['employee_id', 'start_date', 'end_date']);
            $table->dropIndex(['status', 'employee_id']);
        });
    }
};
