<?php

namespace Database\Factories;

use App\Models\Benefit;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition()
    {
        $start = fake()->dateTimeBetween('-2 years', 'now');
        $end = (clone $start)->modify('+' . fake()->numberBetween(30, 365) . ' days');

        return [
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(['health', 'transport', 'meal', 'other']),
            'provider' => fake()->company(),
            'details' => fake()->optional()->sentence(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'active' => fake()->boolean(90),
        ];
    }
}
