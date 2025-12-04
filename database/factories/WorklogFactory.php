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

        // Generate break times (lunch break, typically ~1 hour)
        $start = Carbon::createFromFormat('H:i:s', $startTime);
        $end = Carbon::createFromFormat('H:i:s', $endTime);
        
        // Break typically starts around 12:00-13:00 and lasts 1 hour
        $breakStartHour = $this->faker->numberBetween(12, 13);
        $breakStartMinute = $this->faker->numberBetween(0, 59);
        $breakStart = sprintf('%02d:%02d:00', $breakStartHour, $breakStartMinute);
        
        // Break duration between 30 minutes and 2 hours
        $breakDurationMinutes = $this->faker->numberBetween(30, 120);
        $breakEnd = Carbon::createFromFormat('H:i:s', $breakStart)
            ->addMinutes($breakDurationMinutes)
            ->format('H:i:s');

        // Calculate hours worked (total time minus break)
        $totalHours = $start->floatDiffInHours($end);
        $bStart = Carbon::createFromFormat('H:i:s', $breakStart);
        $bEnd = Carbon::createFromFormat('H:i:s', $breakEnd);
        $breakDuration = $bStart->floatDiffInHours($bEnd);
        $hoursWorked = max(0, $totalHours - $breakDuration);
        $extraHours = max(0, (int)($hoursWorked - 8)); // Only count whole hours

        return [
            'employee_id' => $employee,
            'work_date' => $workDate->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
            'hours_worked' => (int)max(0, $hoursWorked),
            'extra_hours' => $extraHours,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
