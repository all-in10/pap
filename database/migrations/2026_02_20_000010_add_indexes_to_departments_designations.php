<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds indexes to lookup tables (departments, designations).
     * Improves performance of dropdown/select lists in forms and filtering.
     * - departments.name: Searches and autocomplete in forms
     * - designations.name: Already UNIQUE, but explicit index improves seek performance
     * - designations.level: Filtering by seniority level
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('designations', function (Blueprint $table) {
            $table->dropIndex(['level']);
        });
    }
};
