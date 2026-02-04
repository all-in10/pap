<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('denies access to panel when user role does not match and shows login path', function () {
    // Create a user with HR role
    $user = User::factory()->create(['role' => 'HR']);

    $this->actingAs($user)
        ->get('/admin')
        ->assertStatus(403)
        ->assertSee('/hr/login');
});
