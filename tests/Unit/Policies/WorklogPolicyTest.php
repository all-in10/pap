<?php

use App\Models\User;
use App\Models\Worklog;
use App\Policies\WorklogPolicy;

test('WorklogPolicy allows HR/Admin/Root to view any', function () {
    $policy = new WorklogPolicy();

    $root = new User(); $root->role = User::ROLE_ROOT;
    expect($policy->viewAny($root))->toBeTrue();

    $admin = new User(); $admin->role = User::ROLE_ADMIN;
    expect($policy->viewAny($admin))->toBeTrue();

    $hr = new User(); $hr->role = User::ROLE_HR;
    expect($policy->viewAny($hr))->toBeTrue();
});

test('WorklogPolicy allows employee to view own worklog only', function () {
    $policy = new WorklogPolicy();
    $user = new User(); $user->role = User::ROLE_EMPLOYEE;
    $user->employee = (object) ['id' => 10];

    $own = new Worklog(); $own->employee_id = 10;
    expect($policy->view($user, $own))->toBeTrue();

    $other = new Worklog(); $other->employee_id = 11;
    expect($policy->view($user, $other))->toBeFalse();
});

test('WorklogPolicy create/update/delete rules', function () {
    $policy = new WorklogPolicy();
    $employee = new User(); $employee->role = User::ROLE_EMPLOYEE;
    expect($policy->create($employee))->toBeTrue();
    expect($policy->update($employee, new Worklog()))->toBeFalse();

    $admin = new User(); $admin->role = User::ROLE_ADMIN;
    expect($policy->update($admin, new Worklog()))->toBeTrue();
    expect($policy->delete($admin, new Worklog()))->toBeTrue();
});
