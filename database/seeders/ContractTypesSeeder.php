<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractType;

class ContractTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'sem_termo', 'label' => 'Contrato sem termo'],
            ['name' => 'termo_certo', 'label' => 'Contrato a termo certo'],
            ['name' => 'termo_incerto', 'label' => 'Contrato a termo incerto'],
            ['name' => 'temporario', 'label' => 'Contrato de trabalho temporário'],
            ['name' => 'tempo_parcial', 'label' => 'Contrato de trabalho a tempo parcial'],
            ['name' => 'prestacao_servicos', 'label' => 'Contrato de prestação de serviços (recibos verdes)'],
        ];

        foreach ($types as $type) {
            ContractType::firstOrCreate(['name' => $type['name']], ['label' => $type['label']]);
        }
    }
}
