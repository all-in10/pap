<?php

use App\Models\FlexibleSchedule;
use App\Models\Employee;
use App\Models\Designation;

describe('FlexibleSchedule', function () {
    it('can validate flexible schedule', function () {
        $schedule = FlexibleSchedule::make([
            'min_daily_hours' => 6.5,
            'max_daily_hours' => 9.5,
            'flex_days_per_week' => 3,
        ]);

        expect($schedule->isValid())->toBeTrue();
    });

    it('rejects invalid flexible schedule (max < min)', function () {
        $schedule = FlexibleSchedule::make([
            'min_daily_hours' => 10,
            'max_daily_hours' => 8,
            'flex_days_per_week' => 3,
        ]);

        expect($schedule->isValid())->toBeFalse();
    });

    it('rejects flexible schedule exceeding 40 hours weekly', function () {
        $schedule = FlexibleSchedule::make([
            'min_daily_hours' => 6,
            'max_daily_hours' => 10,
            'flex_days_per_week' => 5, // 10 * 5 = 50h > 40h
        ]);

        expect($schedule->isValid())->toBeFalse();
    });

    it('calculates total flexible weekly hours', function () {
        $schedule = FlexibleSchedule::make([
            'max_daily_hours' => 8,
            'flex_days_per_week' => 4,
        ]);

        expect($schedule->getTotalFlexibleWeeklyHours())->toBe(32.0);
    });

    it('has type options', function () {
        $types = FlexibleSchedule::getTypeOptions();
        expect($types)->toHaveKeys(['fixed', 'flexible', '4x3']);
    });
});
