<?php

use App\Models\User;
use App\Models\Contract;
use App\Policies\ContractPolicy;

test('ContractPolicy hr/admin/root can view and create', function () {
    $policy = new ContractPolicy();

    $hr = new User(); $hr->role = User::ROLE_HR;
    expect($policy->viewAny($hr))->toBeTrue();
    expect($policy->create($hr))->toBeTrue();

    $admin = new User(); $admin->role = User::ROLE_ADMIN;
    expect($policy->viewAny($admin))->toBeTrue();
    expect($policy->create($admin))->toBeTrue();
});

test('ContractPolicy employee can view own contract only', function () {
    $policy = new ContractPolicy();
    $user = new User(); $user->role = User::ROLE_EMPLOYEE;
    $user->employee = (object) ['id' => 20];

    $own = new Contract(); $own->employee_id = 20;
    expect($policy->view($user, $own))->toBeTrue();

    $other = new Contract(); $other->employee_id = 21;
    expect($policy->view($user, $other))->toBeFalse();
});
