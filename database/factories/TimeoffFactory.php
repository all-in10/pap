<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Timeoff;
use App\Models\Employee;

class TimeoffFactory extends Factory
{
    protected $model = Timeoff::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-3 months', '+3 months');
        $endDate = $this->faker->dateTimeBetween($startDate, (clone $startDate)->modify('+30 days'));

        $types = ['vacation', 'sick_leave', 'personal_leave', 'other'];
        $statuses = ['pending', 'approved', 'rejected'];

        return [
            'employee_id' => Employee::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'type' => $this->faker->randomElement($types),
            'status' => $this->faker->randomElement($statuses),
            'reason' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
        ]);
    }
}