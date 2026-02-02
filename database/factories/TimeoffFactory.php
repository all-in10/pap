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
        $start = $this->faker->dateTimeBetween('-3 months', '+1 month');
        $end = (clone $start)->modify('+' . $this->faker->numberBetween(1, 10) . ' days');
        $hours = $this->faker->randomFloat(2, 1, 80);

        return [
            'employee_id' => Employee::factory(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'hours' => $hours,
            'type' => $this->faker->randomElement(['vacation', 'sick', 'personal']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'denied']),
            'reason' => $this->faker->optional()->sentence(),
        ];
    }
}
