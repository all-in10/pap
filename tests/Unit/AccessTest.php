<?php

use App\Models\User;
use App\Services\Access;

test('Access::hasRole recognizes roles', function () {
    $user = new User();
    $user->role = User::ROLE_ROOT;
    expect(Access::hasRole('root', $user))->toBeTrue();
    expect(Access::hasRole('admin', $user))->toBeTrue();
    expect(Access::hasRole('hr', $user))->toBeTrue();

    $user->role = User::ROLE_ADMIN;
    expect(Access::hasRole('admin', $user))->toBeTrue();
    expect(Access::hasRole('hr', $user))->toBeTrue();

    $user->role = User::ROLE_HR;
    expect(Access::hasRole('hr', $user))->toBeTrue();
    expect(Access::hasRole('employee', $user))->toBeFalse();

    $user->role = User::ROLE_EMPLOYEE;
    expect(Access::hasRole('employee', $user))->toBeTrue();
    expect(Access::hasRole('admin', $user))->toBeFalse();
});
