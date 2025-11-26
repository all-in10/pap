<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Worklog;
use App\Models\Employee;
use Carbon\Carbon;

class WorklogFactory extends Factory
{
    protected $model = Worklog::class;

    public function definition(): array
    {
        $employee = Employee::factory();
        $workDate = $this->faker->dateTimeBetween('-30 days', 'now');
        
        // Generate random start time between 8:00 and 9:30
        $startHour = $this->faker->numberBetween(8, 9);
        $startMinute = $this->faker->numberBetween(0, 59);
        $startTime = sprintf('%02d:%02d:00', $startHour, $startMinute);
        
        // Generate end time 8-10 hours after start time
        $hoursToAdd = $this->faker->numberBetween(8, 10);
        $endTime = Carbon::createFromFormat('H:i:s', $startTime)
            ->addHours($hoursToAdd)
            ->format('H:i:s');

        // Calculate hours worked and extra hours
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);
        $hoursWorked = $start->floatDiffInHours($end);
        $extraHours = max(0, $hoursWorked - 8);

        return [
            'employee_id' => $employee,
            'work_date' => $workDate->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'hours_worked' => round($hoursWorked, 2),
            'extra_hours' => round($extraHours, 2),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
