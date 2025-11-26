<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hoursbank;
use App\Models\Employee;

class HoursbankFactory extends Factory
{
    protected $model = Hoursbank::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'total_hours' => $this->faker->randomFloat(2, 0, 50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
