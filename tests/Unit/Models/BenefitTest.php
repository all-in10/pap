<?php

use App\Models\Benefit;
use App\Models\EmployeeBenefit;

describe('Benefit', function () {
    it('has benefit type options', function () {
        $types = Benefit::getTypeOptions();
        expect($types)->toHaveKeys(['monthly', 'annual', 'one_time']);
    });

    it('creates a benefit with correct casting', function () {
        $benefit = Benefit::make([
            'name' => 'Vale Refeição',
            'type' => 'monthly',
            'value' => 350.00,
            'is_active' => true,
        ]);

        expect($benefit->value)->toBe(350.00)
            ->and($benefit->is_active)->toBeTrue();
    });
});

describe('EmployeeBenefit', function () {
    it('checks if benefit is currently active', function () {
        $employeeBenefit = EmployeeBenefit::make([
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);

        expect($employeeBenefit->isActive())->toBeTrue();
    });

    it('marks expired benefits as inactive', function () {
        $employeeBenefit = EmployeeBenefit::make([
            'start_date' => now()->subMonths(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
        ]);

        expect($employeeBenefit->isActive())->toBeFalse();
    });

    it('marks future benefits as inactive', function () {
        $employeeBenefit = EmployeeBenefit::make([
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
        ]);

        expect($employeeBenefit->isActive())->toBeFalse();
    });

    it('treats indefinite benefits as active', function () {
        $employeeBenefit = EmployeeBenefit::make([
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => null,
        ]);

        expect($employeeBenefit->isActive())->toBeTrue();
    });

    it('formats benefit value as currency', function () {
        $employeeBenefit = EmployeeBenefit::make([
            'value_override' => 350.50,
        ]);

        $formatted = $employeeBenefit->getFormattedValue();
        expect($formatted)->toContain('R$')
            ->and($formatted)->toContain('350');
    });
});
