<?php

use App\Models\PerformanceReview;

describe('PerformanceReview', function () {
    it('has period options', function () {
        $periods = PerformanceReview::getPeriodOptions();
        expect($periods)->toHaveKeys(['semestral', 'anual', 'probation']);
    });

    it('has rating options', function () {
        $ratings = PerformanceReview::getRatingOptions();
        expect($ratings)->toHaveKeys(['1', '2', '3', '4', '5']);
    });

    it('validates rating is between 1 and 5', function () {
        $review = PerformanceReview::make([
            'rating' => 4.5,
        ]);

        expect($review->rating)->toBe(4.5);
    });

    it('validates goals met is between 0 and 100', function () {
        $review = PerformanceReview::make([
            'goals_met' => 85,
        ]);

        expect($review->goals_met)->toBe(85);
    });

    it('stores strengths as array', function () {
        $review = PerformanceReview::make([
            'strengths' => ['Liderança', 'Comunicação'],
        ]);

        expect($review->strengths)->toBeArray()
            ->and(count($review->strengths))->toBe(2);
    });

    it('stores improvements as array', function () {
        $review = PerformanceReview::make([
            'improvements' => ['Pontualidade', 'Documentação'],
        ]);

        expect($review->improvements)->toBeArray()
            ->and(count($review->improvements))->toBe(2);
    });
});
