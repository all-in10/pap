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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Cria alguns cargos
        Designation::factory()->count(5)->create();

        // Cria alguns países, estados e cidades
        Country::factory()
            ->count(3)
            ->has(State::factory()
                ->count(5)
                ->has(City::factory()->count(3))
            )
            ->create();

        // Cria departamentos
        Department::factory()->count(4)->create();

        // Cria funcionários com relação a país, estado, cidade, departamento e cargo
        Employee::factory()->count(10)->create();

        // Garante que os tipos de contrato existem antes de criar contratos
        $this->call(\Database\Seeders\ContractTypesSeeder::class);

        // Cria contratos para os funcionários
        Employee::all()->each(function($employee) {
            Contract::factory()->count(1)->create([
                'employee_id' => $employee->id,
            ]);
        });

        // Atribui usuários a alguns Employees (relação 1:1)
        $employees = \App\Models\Employee::take(5)->get();
        foreach ($employees as $index => $employee) {
            // Só cria user se não existir para o employee
            if (!\App\Models\User::where('employee_id', $employee->id)->exists()) {
                $user = \App\Models\User::factory()->create([
                    'name' => $employee->first_name . ' ' . $employee->last_name,
                    'employee_id' => $employee->id
                ]);
            }
        }

        // Cria categorias de licença
        $categoryMap = [];
        $categoryData = [
            ['key' => 'parentalidade', 'label' => '👶 Parentalidade'],
            ['key' => 'saude', 'label' => '🏥 Saúde'],
            ['key' => 'familia', 'label' => '👨‍👩‍👧 Família'],
            ['key' => 'formacao', 'label' => '🎓 Formação e Vida Pessoal'],
            ['key' => 'outras', 'label' => '🪖 Outras'],
        ];
        foreach ($categoryData as $cat) {
            $category = \App\Models\TimeoffCategory::firstOrCreate(['key' => $cat['key']], ['label' => $cat['label']]);
            $categoryMap[$cat['key']] = $category->id;
        }

        // Cria licenças (Timeoff) para cada tipo e categoria
        $employees = \App\Models\Employee::all();
        // Cria licenças (Timeoff) para cada tipo e categoria
        $timeoffTypes = \App\Models\Timeoff::TYPES;
        foreach ($timeoffTypes as $type => $info) {
            $employee = $employees->random();
            $categoryId = $categoryMap[$info['category']] ?? null;
            \App\Models\Timeoff::create([
                'employee_id' => $employee->id,
                'start_date' => now()->subDays(rand(1, 365)),
                'end_date' => now()->addDays(rand(1, 30)),
                'type' => $type,
                'category_id' => $categoryId,
                'status' => 'pending',
                'reason' => $info['label'],
            ]);
        }
    }
}
