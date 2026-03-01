<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 month', 'now');
        $enter = (clone $date)->setTime(8, 0);
        $exit = (clone $enter)->modify('+9 hours'); // 1h extra (inclui almoço de 1h)

        return [
            'employee_id' => Employee::factory(),
            'date' => $date->format('Y-m-d'),
            'enter_time' => $enter->format('H:i:s'),
            'exit_time' => $exit->format('H:i:s'),
            'break_minutes' => 60,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
