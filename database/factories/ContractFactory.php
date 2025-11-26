<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\ContractType;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        $statuses = ['active', 'terminated', 'suspended'];
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $endDate = $this->faker->optional()->dateTimeBetween($startDate, '+2 years');

        $contractType = ContractType::inRandomOrder()->first() ?? ContractType::factory()->create();
        $designation = Designation::inRandomOrder()->first() ?? Designation::factory()->create();

        return [
            'designation_id' => $designation->id,
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
