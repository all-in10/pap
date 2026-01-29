<?php

namespace Database\Factories;

use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceReviewFactory extends Factory
{
    protected $model = PerformanceReview::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'reviewer_id' => User::factory(),
            'review_period' => $this->faker->randomElement(['semestral', 'anual', 'probation']),
            'rating' => $this->faker->randomFloat(1, 1, 5),
            'comments' => $this->faker->paragraph(),
            'goals_met' => $this->faker->numberBetween(0, 100),
            'strengths' => $this->faker->words(3),
            'improvements' => $this->faker->words(3),
            'recommended_raise' => $this->faker->randomFloat(2, 0, 20),
        ];
    }
}
