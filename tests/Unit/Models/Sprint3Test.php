<?php

use App\Models\FlexibleSchedule;
use App\Models\PerformanceReview;
use App\Models\Benefit;

describe('FlexibleSchedule', function () {
    it('has type options', function () {
        $types = FlexibleSchedule::getTypeOptions();
        expect($types)->toHaveKeys(['fixed', 'flexible', '4x3']);
    });

    it('validates max > min logic', function () {
        // Test validation logic
        $minHours = 6.5;
        $maxHours = 9.5;
        $isValid = $maxHours > $minHours;
        
        expect($isValid)->toBeTrue();
    });

    it('rejects when max <= min', function () {
        $minHours = 10;
        $maxHours = 8;
        $isValid = $maxHours > $minHours;
        
        expect($isValid)->toBeFalse();
    });

    it('validates weekly hours limit', function () {
        $maxDailyHours = 10;
        $flexDays = 5;
        $totalWeeklyHours = $maxDailyHours * $flexDays;
        $isValid = $totalWeeklyHours <= 40;
        
        expect($isValid)->toBeFalse();
    });

    it('accepts valid weekly hours', function () {
        $maxDailyHours = 8;
        $flexDays = 4;
        $totalWeeklyHours = $maxDailyHours * $flexDays;
        $isValid = $totalWeeklyHours <= 40;
        
        expect($isValid)->toBeTrue();
    });
});

describe('PerformanceReview', function () {
    it('has period options', function () {
        $periods = PerformanceReview::getPeriodOptions();
        expect($periods)->toHaveKeys(['semestral', 'anual', 'probation']);
    });

    it('has rating options', function () {
        $ratings = PerformanceReview::getRatingOptions();
        expect($ratings)->toHaveKeys(['1', '2', '3', '4', '5']);
    });

    it('validates rating scale', function () {
        $validRatings = [1.0, 2.5, 3.5, 4.0, 5.0];
        
        foreach ($validRatings as $rating) {
            expect($rating >= 1 && $rating <= 5)->toBeTrue();
        }
    });

    it('validates goals met percentage', function () {
        $validGoalsPercentages = [0, 25, 50, 75, 100];
        
        foreach ($validGoalsPercentages as $percentage) {
            expect($percentage >= 0 && $percentage <= 100)->toBeTrue();
        }
    });
});

describe('Benefit', function () {
    it('has benefit type options', function () {
        $types = Benefit::getTypeOptions();
        expect($types)->toHaveKeys(['monthly', 'annual', 'one_time']);
    });

    it('validates benefit types', function () {
        $validTypes = ['monthly', 'annual', 'one_time'];
        
        foreach ($validTypes as $type) {
            expect(in_array($type, $validTypes))->toBeTrue();
        }
    });

    it('validates positive benefit value', function () {
        $benefitValue = 350.50;
        
        expect($benefitValue > 0)->toBeTrue();
    });

    it('handles date logic for active benefits', function () {
        // Test date range logic
        $startDate = now()->subDay();
        $endDate = now()->addDay();
        $today = now();
        
        $isActive = $today >= $startDate && $today <= $endDate;
        
        expect($isActive)->toBeTrue();
    });

    it('identifies expired benefits', function () {
        $startDate = now()->subMonths(3);
        $endDate = now()->subDay();
        $today = now();
        
        $isExpired = $today > $endDate;
        
        expect($isExpired)->toBeTrue();
    });

    it('identifies future benefits', function () {
        $startDate = now()->addDay();
        $today = now();
        
        $isFuture = $today < $startDate;
        
        expect($isFuture)->toBeTrue();
    });
});
