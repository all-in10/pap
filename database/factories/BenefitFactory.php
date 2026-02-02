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
        $start = $this->faker->dateTimeBetween('-2 years', 'now');
        $end = (clone $start)->modify('+' . $this->faker->numberBetween(30, 365) . ' days');

        return [
            'employee_id' => Employee::factory(),
            'type' => $this->faker->randomElement(['health', 'transport', 'meal', 'other']),
            'provider' => $this->faker->company(),
            'details' => $this->faker->optional()->sentence(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'active' => $this->faker->boolean(90),
        ];
    }
}
