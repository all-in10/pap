<?php

use Illuminate\Support\Facades\Route;

it('has filament admin and employee dashboard routes defined', function () {
    $admin = route('filament.admin.pages.dashboard');
    $employee = route('filament.employee.pages.dashboard');

    expect($admin)->toBeString();
    expect($employee)->toBeString();
});
