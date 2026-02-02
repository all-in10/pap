<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Employee;
use App\Models\ContractType;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Contract::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $statuses = ['active', 'terminated', 'suspended'];

        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $endDate = $this->faker->optional()->dateTimeBetween($startDate, '+2 years');

        // Escolhe um ContractType existente ou cria um por padrão
        $contractType = ContractType::inRandomOrder()->first();
        if (!$contractType) {
            $contractType = ContractType::create([
                'name' => 'sem_termo',
                'label' => 'Contrato sem termo',
            ]);
        }

        return [
            'employee_id' => Employee::factory(),
            'contract_type_id' => $contractType->id,
            'salary' => $this->faker->randomFloat(2, 1000, 10000),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'status' => $this->faker->randomElement($statuses),
            'date_hired' => $startDate->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
