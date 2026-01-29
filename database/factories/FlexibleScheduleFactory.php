<?php

namespace Database\Factories;

use App\Models\FlexibleSchedule;
use App\Models\Employee;
use App\Models\Designation;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlexibleScheduleFactory extends Factory
{
    protected $model = FlexibleSchedule::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'designation_id' => Designation::factory(),
            'type' => $this->faker->randomElement(['fixed', 'flexible', '4x3']),
            'min_daily_hours' => 6.5,
            'max_daily_hours' => 9.5,
            'flex_days_per_week' => $this->faker->numberBetween(1, 5),
            'is_active' => true,
        ];
    }
}
