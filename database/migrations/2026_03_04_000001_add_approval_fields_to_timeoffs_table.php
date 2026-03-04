<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('timeoffs', function (Blueprint $table) {
            // Adiciona campo de quem aprovou o pedido
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('status')
                ->comment('Usuário que aprovou o pedido');

            // Adiciona timestamp de quando foi aprovado
            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by')
                ->comment('Data e hora da aprovação');

            // Índices para melhorar performance de queries
            $table->index(['approved_by', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('timeoffs', function (Blueprint $table) {
            $table->dropIndex(['approved_by', 'approved_at']);
            $table->dropForeignIdFor(\App\Models\User::class, 'approved_by');
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }
};
