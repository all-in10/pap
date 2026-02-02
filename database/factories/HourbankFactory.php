<?php

namespace Database\Factories;

use App\Models\Hourbank;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class HourbankFactory extends Factory
{
    protected $model = Hourbank::class;

    public function definition()
    {
        return [
            'employee_id' => Employee::factory(),
            'balance_hours' => $this->faker->randomFloat(2, -40, 200),
            'last_accrual_date' => $this->faker->optional()->date(),
        ];
    }
}
