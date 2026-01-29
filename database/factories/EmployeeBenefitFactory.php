<?php

namespace Database\Factories;

use App\Models\EmployeeBenefit;
use App\Models\Employee;
use App\Models\Benefit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeBenefitFactory extends Factory
{
    protected $model = EmployeeBenefit::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'benefit_id' => Benefit::factory(),
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'value_override' => null,
            'approved_by' => User::factory(),
        ];
    }
}
