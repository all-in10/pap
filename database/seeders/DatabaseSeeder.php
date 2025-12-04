<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Worklog;
use App\Models\Hoursbank;
use App\Models\Timeoff;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria cargos
        Designation::factory()->count(5)->create();

        // 2. Cria países, estados e cidades
        Country::factory()
            ->count(3)
            ->has(State::factory()
                ->count(5)
                ->has(City::factory()->count(3))
            )
            ->create();

        // 3. Cria departamentos
        Department::factory()->count(4)->create();

        // 4. Cria tipos de contrato sem duplicar
        $types = [
            ['name'=>'Tempo completo','category'=>'full_time','description'=>'Jornada de 40h semanais.'],
            ['name'=>'Tempo parcial','category'=>'full_time','description'=>'Menos de 40h semanais.'],
            ['name'=>'Contrato a termo certo','category'=>'temporary','description'=>'Duração definida, renovável.'],
            ['name'=>'Contrato a termo incerto','category'=>'temporary','description'=>'Termina quando cessa a necessidade.'],
            ['name'=>'Trabalho intermitente','category'=>'temporary','description'=>'Períodos alternados de trabalho e inatividade.'],
            ['name'=>'Estágio profissional','category'=>'internship','description'=>'Estágio profissional.'],
            ['name'=>'Outro / não definido','category'=>'non_defined','description'=>'Tipo não categorizado.'],
        ];

        foreach ($types as $type) {
            ContractType::updateOrCreate(['name' => $type['name']], $type);
        }

        // 5. Cria funcionários
        $employees = Employee::factory()->count(10)->create();

        // 6. Cria contratos para cada funcionário
        $contractTypes = ContractType::all();

        foreach ($employees as $employee) {
            Contract::factory()->create([
                'employee_id' => $employee->id,
                'contract_type_id' => $contractTypes->random()->id,
            ]);
        }

        // 7. Cria registros de horas (Worklogs)
        // O Hoursbank é criado automaticamente quando Employee é criado, então não duplicamos aqui
        foreach ($employees as $employee) {
            Worklog::factory()->count(5)->create([
                'employee_id' => $employee->id,
            ]);
        }

        // 8. Cria pedidos de folga (Timeoff)
        foreach ($employees as $employee) {
            Timeoff::factory()->count(rand(1, 3))->create([
                'employee_id' => $employee->id,
            ]);
        }
    }
}
