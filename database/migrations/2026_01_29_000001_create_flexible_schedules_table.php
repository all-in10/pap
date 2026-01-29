<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flexible_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['fixed', 'flexible', '4x3'])->default('fixed');
            $table->decimal('min_daily_hours', 3, 2)->default(6.50); // 6h 30min
            $table->decimal('max_daily_hours', 3, 2)->default(9.50); // 9h 30min
            $table->integer('flex_days_per_week')->default(3); // 3 dias com flexibilidade
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['employee_id', 'is_active']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flexible_schedules');
    }
};
