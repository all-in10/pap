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
use App\Models\FlexibleSchedule;
use App\Models\PerformanceReview;
use App\Models\Benefit;
use App\Models\EmployeeBenefit;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cria cargos com valores fixos
        $designations = [
            ['name' => 'Desenvolvedor Senior', 'description' => 'Desenvolvedor experiente', 'level' => 'senior', 'base_salary' => 8000.00],
            ['name' => 'Desenvolvedor Pleno', 'description' => 'Desenvolvedor intermediário', 'level' => 'pleno', 'base_salary' => 6000.00],
            ['name' => 'Desenvolvedor Junior', 'description' => 'Desenvolvedor iniciante', 'level' => 'junior', 'base_salary' => 4000.00],
            ['name' => 'Analista de Sistemas', 'description' => 'Analista de sistemas', 'level' => 'pleno', 'base_salary' => 7000.00],
            ['name' => 'Gerente de TI', 'description' => 'Gerente de tecnologia', 'level' => 'senior', 'base_salary' => 10000.00],
        ];

        $createdDesignations = [];
        foreach ($designations as $designation) {
            $createdDesignations[] = Designation::create($designation);
        }

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

        // 5. Cria funcionários com designações criadas
        $employees = [];
        for ($i = 0; $i < 10; $i++) {
            $employees[] = Employee::factory()->create([
                'designation_id' => $createdDesignations[array_rand($createdDesignations)]->id,
            ]);
        }

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

        // 8. Cria pedidos de folga (Timeoff) - usando status pending para evitar conflitos
        foreach ($employees as $employee) {
            Timeoff::factory()->count(rand(1, 3))->create([
                'employee_id' => $employee->id,
                'status' => 'pending', // Usando pending para evitar validação de overlapping
            ]);
        }

        // ============== SPRINT 3 - SEEDING ==============

        // 9. Cria Jornadas Flexíveis para funcionários
        $scheduleTypes = ['fixed', 'flexible', '4x3'];
        foreach ($employees as $employee) {
            FlexibleSchedule::create([
                'employee_id' => $employee->id,
                'designation_id' => $employee->designation_id,
                'type' => $scheduleTypes[array_rand($scheduleTypes)],
                'min_daily_hours' => 6.5,
                'max_daily_hours' => 9.5,
                'flex_days_per_week' => rand(1, 3),
                'is_active' => true,
            ]);
        }

        // 10. Cria Benefícios Corporativos
        $benefits = [
            [
                'name' => 'Vale Refeição',
                'description' => 'Vale para alimentação diária',
                'type' => 'monthly',
                'value' => 300.00,
                'is_active' => true,
            ],
            [
                'name' => 'Vale Transporte',
                'description' => 'Vale para transporte público',
                'type' => 'monthly',
                'value' => 150.00,
                'is_active' => true,
            ],
            [
                'name' => 'Auxílio Saúde',
                'description' => 'Plano de saúde corporativo',
                'type' => 'monthly',
                'value' => 500.00,
                'is_active' => true,
            ],
            [
                'name' => 'Bônus Anual',
                'description' => 'Bônus de desempenho anual',
                'type' => 'annual',
                'value' => 2000.00,
                'is_active' => true,
            ],
            [
                'name' => 'Auxílio Educação',
                'description' => 'Auxílio para cursos e treinamento',
                'type' => 'annual',
                'value' => 1200.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cesta Básica',
                'description' => 'Cesta de alimentos',
                'type' => 'monthly',
                'value' => 200.00,
                'is_active' => true,
            ],
        ];

        $createdBenefits = [];
        foreach ($benefits as $benefitData) {
            $createdBenefits[] = Benefit::create($benefitData);
        }

        // 11. Atribui Benefícios aos Funcionários
        $approvers = User::inRandomOrder()->limit(3)->get();
        
        foreach ($employees as $employee) {
            // Cada funcionário recebe 3-5 benefícios aleatórios
            $randomBenefits = collect($createdBenefits)
                ->random(rand(3, 5))
                ->unique('id');

            foreach ($randomBenefits as $benefit) {
                EmployeeBenefit::create([
                    'employee_id' => $employee->id,
                    'benefit_id' => $benefit->id,
                    'start_date' => now()->toDateString(),
                    'end_date' => null,
                    'value_override' => null,
                    'approved_by' => $approvers->random()->id,
                ]);
            }
        }

        // 12. Cria Avaliações de Desempenho
        $reviewers = User::inRandomOrder()->limit(5)->get();
        
        foreach ($employees as $employee) {
            // Cria 2-3 avaliações por funcionário
            for ($i = 0; $i < rand(2, 3); $i++) {
                PerformanceReview::create([
                    'employee_id' => $employee->id,
                    'reviewer_id' => $reviewers->random()->id,
                    'review_period' => ['semestral', 'anual', 'probation'][array_rand(['semestral', 'anual', 'probation'])],
                    'rating' => round(rand(30, 50) / 10, 1), // 3.0 a 5.0
                    'comments' => 'Avaliação de desempenho realizada com sucesso. Funcionário demonstrou competência nas atividades atribuídas.',
                    'goals_met' => rand(60, 100),
                    'strengths' => json_encode(['Comprometimento', 'Proatividade', 'Trabalho em equipe']),
                    'improvements' => json_encode(['Comunicação', 'Gestão de tempo']),
                    'recommended_raise' => round(rand(0, 20) / 100, 2), // 0% a 20%
                ]);
            }
        }
    }
}
