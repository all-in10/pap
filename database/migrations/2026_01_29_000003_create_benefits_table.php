<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: Vale Refeição, Vale Transporte
            $table->text('description')->nullable();
            $table->enum('type', ['monthly', 'annual', 'one_time']); // Monthly/Annual/One-time
            $table->decimal('value', 10, 2); // Default value
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benefits');
    }
};
