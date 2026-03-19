<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adiciona campos de gestão de saldo de férias na tabela employees.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('vacation_balance')->default(22)->after('is_active')->comment('Saldo de férias do ano atual');
            $table->integer('vacation_year')->default(now()->year)->after('vacation_balance')->comment('Ano do saldo de férias');
            $table->timestamp('last_balance_renewal_at')
                ->nullable()
                ->after('vacation_year')
                ->comment('Última atualização automática do saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['vacation_balance', 'vacation_year', 'last_balance_renewal_at']);
        });
    }
};
