<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('review_period', ['semestral', 'anual', 'probation']);
            $table->decimal('rating', 2, 1); // 1.0 to 5.0
            $table->text('comments')->nullable();
            $table->integer('goals_met')->default(0); // 0-100 percentage
            $table->json('strengths')->nullable(); // Array of strengths
            $table->json('improvements')->nullable(); // Array of improvement areas
            $table->decimal('recommended_raise', 5, 2)->default(0); // Percentage
            $table->timestamps();

            $table->index(['employee_id', 'created_at']);
            $table->index('review_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
