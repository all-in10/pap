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

        // Cria contratos para os funcionários
        Employee::all()->each(function($employee) {
            Contract::factory()->count(1)->create([
                'employee_id' => $employee->id,
            ]);
        });
    }
}
