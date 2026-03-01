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
        $start = $this->faker->dateTimeBetween('-1 months', 'now');
        $duration = $this->faker->randomFloat(2, 1, 8);
        $end = (clone $start)->modify('+' . floor($duration) . ' hours');

        return [
            'employee_id' => Employee::factory(),
            'date' => $start->format('Y-m-d'),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'duration' => $duration,
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['regular', 'overtime']),
            'approved' => $this->faker->boolean(75),
        ];
    }
}
