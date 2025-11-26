<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ContractType;

class ContractTypeFactory extends Factory
{
    protected $model = ContractType::class;

    protected static array $types = [
        ['name'=>'Tempo completo','category'=>'full_time','description'=>'Jornada de 40h semanais.'],
        ['name'=>'Tempo parcial','category'=>'full_time','description'=>'Menos de 40h semanais.'],
        ['name'=>'Contrato a termo certo','category'=>'temporary','description'=>'Duração definida, renovável.'],
        ['name'=>'Contrato a termo incerto','category'=>'temporary','description'=>'Termina quando cessa a necessidade.'],
        ['name'=>'Trabalho intermitente','category'=>'temporary','description'=>'Períodos alternados de trabalho e inatividade.'],
        ['name'=>'Estágio profissional','category'=>'internship','description'=>'Estágio profissional.'],
        ['name'=>'Outro / não definido','category'=>'non_defined','description'=>'Tipo não categorizado.'],
    ];

    protected static int $index = 0;

    public function definition(): array
    {
        $type = self::$types[self::$index % count(self::$types)];
        self::$index++;
        return $type;
    }
}
