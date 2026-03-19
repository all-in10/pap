<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cria tabela de férias anuais com rastreamento de saldo e aprovação.
     */
    public function up(): void
    {
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            // Datas e duração
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_taken');  // Dias utilizados (end_date - start_date + 1)

            // Saldo e controle de ano
            $table->integer('vacation_year')->default(now()->year);  // Ano civil das férias
            $table->integer('balance_at_creation')->default(22);  // Saldo no momento de criação

            // Status e aprovação
            $table->string('status')->default('pending');  // pending, approved, rejected
            $table->text('reason')->nullable();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Timestamps e soft delete
            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index(['employee_id', 'vacation_year']);
            $table->index(['status', 'approved_by']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
