<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Garante que a tabela contract_types exista e esteja populada (se necessário)
        if (!Schema::hasTable('contract_types')) {
            Schema::create('contract_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('label');
                $table->timestamps();
            });

            DB::table('contract_types')->insert([
                ['name' => 'sem_termo', 'label' => 'Contrato sem termo', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'termo_certo', 'label' => 'Contrato a termo certo', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'termo_incerto', 'label' => 'Contrato a termo incerto', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'temporario', 'label' => 'Contrato de trabalho temporário', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'tempo_parcial', 'label' => 'Contrato de trabalho a tempo parcial', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'prestacao_servicos', 'label' => 'Contrato de prestação de serviços (recibos verdes)', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('contract_type_id')->nullable()->constrained('contract_types')->nullOnDelete();
        });

        // Se existirem contract_types criados, associe contratos existentes ao primeiro tipo (sem termo) quando possível
        if (Schema::hasTable('contract_types') && DB::table('contract_types')->count() > 0) {
            $default = DB::table('contract_types')->where('name', 'sem_termo')->first();
            if (!$default) {
                $default = DB::table('contract_types')->first();
            }
            if ($default) {
                DB::table('contracts')->update(['contract_type_id' => $default->id]);
            }
        }

        // Remova a coluna antiga se existir
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'contract_type')) {
                $table->dropColumn('contract_type');
            }
        });
    }

    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->enum('contract_type', ['full_time', 'temporary', 'internship', 'non_defined'])->default('non_defined');
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_type_id');
        });
    }
};