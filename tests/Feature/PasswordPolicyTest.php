<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects user to change password when must_change_password is true', function () {
    $user = User::factory()->create(['must_change_password' => true]);

    $this->actingAs($user)
        ->get('/')
        ->assertRedirect(route('password.change'));
});

it('allows changing password and clears the flag', function () {
    $user = User::factory()->create(['must_change_password' => true]);

    $this->actingAs($user)
        ->post(route('password.update'), [
            'password' => 'NewStrongPass1!',
            'password_confirmation' => 'NewStrongPass1!',
        ])
        ->assertRedirect('/');

    expect($user->fresh()->must_change_password)->toBeFalse();
});
