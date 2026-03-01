<?php

namespace Database\Factories;

use App\Models\Worklog;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorklogFactory extends Factory
{
    protected $model = Worklog::class;

    public function definition()
    {
        $start = fake()->dateTimeBetween('-1 months', 'now');
        $duration = fake()->randomFloat(2, 1, 8);
        $end = (clone $start)->modify('+' . floor($duration) . ' hours');

        return [
            'employee_id' => Employee::factory(),
            'date' => $start->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'duration' => $duration,
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['regular', 'overtime']),
            'approved' => fake()->boolean(75),
        ];
    }
}
