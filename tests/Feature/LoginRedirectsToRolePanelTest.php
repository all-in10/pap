<?php

/**
 * @mixin \Tests\TestCase
 * @method \Illuminate\Testing\TestResponse withSession(array $data)
 * @method \Illuminate\Testing\TestResponse post(string $uri, array $data = [], array $headers = [])
 */

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects employee to employee panel when intended is a different panel', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_EMPLOYEE,
        'password' => bcrypt('secret'),
    ]);

    /** @var \Tests\TestCase $this */
    $this->withSession(['url.intended' => '/admin'])
        ->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])
        ->assertRedirect('/employee');
});

it('keeps non-panel intended url after login', function () {
    $user = User::factory()->create([
        'role' => User::ROLE_EMPLOYEE,
        'password' => bcrypt('secret'),
    ]);

    /** @var \Tests\TestCase $this */
    $this->withSession(['url.intended' => '/profile'])
        ->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ])
        ->assertRedirect('/profile');
});
