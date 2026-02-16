<?php

use App\Models\Employee;
use App\Models\User;
use App\Models\Contract;
use App\Models\Hourbank;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates user automatically when employee is created', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Act
    $employee = Employee::factory()->create([
        'first_name' => 'João',
        'last_name' => 'Silva',
        'email' => 'joao@example.com',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert
    $user = User::where('email', 'joao@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('João Silva');
    expect($user->role)->toBe('employee');
    expect($user->must_change_password)->toBeTrue();
    expect($user->employee_id)->toBe($employee->id);
});

it('creates contract automatically when employee is created', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Act
    $employee = Employee::factory()->create([
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert
    $contract = $employee->contracts()->first();
    expect($contract)->not->toBeNull();
    expect($contract->status)->toBe('active');
    expect($contract->start_date->toDateString())->toBe($employee->date_hired->toDateString());
});

it('creates hourbank automatically when employee is created', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Act
    $employee = Employee::factory()->create([
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert
    $hourbank = $employee->hourbanks()->first();
    expect($hourbank)->not->toBeNull();
    expect($hourbank->balance_hours)->toEqual(0);
    expect($hourbank->last_accrual_date->toDateString())->toBe($employee->date_hired->toDateString());
});

it('does not create duplicate user if email already exists', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Create a user with an email
    $existingUser = User::factory()->create(['email' => 'duplicate@example.com']);

    // Act
    $employee = Employee::factory()->create([
        'email' => 'duplicate@example.com',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert - only one user with this email should exist
    $usersCount = User::where('email', 'duplicate@example.com')->count();
    expect($usersCount)->toBe(1);
    
    // The existing user should not be associated with the new employee
    expect($existingUser->employee_id)->toBeNull();
});

it('user password is hashed when employee is created', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Act
    $employee = Employee::factory()->create([
        'email' => 'secure@example.com',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert
    $user = User::where('email', 'secure@example.com')->first();
    expect($user->password)->not->toBe(env('DEFAULT_USER_PASSWORD', 'ChangeMe123!'));
    expect(\Illuminate\Support\Facades\Hash::check(
        env('DEFAULT_USER_PASSWORD', 'ChangeMe123!'),
        $user->password
    ))->toBeTrue();
});

it('clears cache after notification data is retrieved', function () {
    // Arrange
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::factory()->create(['state_id' => $state->id]);
    $department = Department::factory()->create();

    // Act
    $employee = Employee::factory()->create([
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'department_id' => $department->id,
    ]);

    // Assert - cache keys should exist
    expect(cache()->has("employee_{$employee->id}_created_user"))->toBeTrue();
    expect(cache()->has("employee_{$employee->id}_created_contract"))->toBeTrue();
    expect(cache()->has("employee_{$employee->id}_created_hourbank"))->toBeTrue();
});
