<?php

namespace Database\Factories;

use App\Models\Timeoff;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeoffFactory extends Factory
{
    protected $model = Timeoff::class;

    public function definition()
    {
        $start = fake()->dateTimeBetween('-3 months', '+1 month');
        $end = (clone $start)->modify('+' . fake()->numberBetween(1, 10) . ' days');
        $hours = fake()->randomFloat(2, 1, 80);

        return [
            'employee_id' => Employee::factory(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'hours' => $hours,
            'type' => fake()->randomElement(['vacation', 'sick', 'personal']),
            'status' => fake()->randomElement(['pending', 'approved', 'denied']),
            'reason' => fake()->optional()->sentence(),
        ];
    }
}
