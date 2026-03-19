<?php

namespace Database\Factories;

use App\Models\Vacation;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacationFactory extends Factory
{
    protected $model = Vacation::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 months', '+2 months');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(3, 15) . ' days');

        $startCarbon = \Carbon\Carbon::instance($startDate);
        $endCarbon = \Carbon\Carbon::instance($endDate);
        $daysTaken = $endCarbon->diffInDays($startCarbon) + 1;

        return [
            'employee_id' => Employee::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days_taken' => $daysTaken,
            'vacation_year' => now()->year,
            'balance_at_creation' => 22,
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'reason' => $this->faker->optional()->sentence(),
            'approved_by' => $this->faker->optional()->numberBetween(1, 5),
            'approved_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
