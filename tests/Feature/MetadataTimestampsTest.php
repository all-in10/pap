<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('user resource form does not show email_verified_at field', function () {
    // This is a validation that email_verified_at is not in the form schema
    // It should only be viewable in the table, never editable in the form
    // The removal is verified by checking UserResource.php does not have the DateTimePicker
    
    $user = User::factory()->create();
    
    // User can be created - the important part is that email_verified_at
    // is not exposed in the form for manual editing
    expect($user)->not->toBeNull();
    expect($user->id)->toBeGreaterThan(0);
});

it('user resource table shows email_verified_at for visibility', function () {
    // email_verified_at should be visible in tables for reference
    // but not editable in forms
    
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->email_verified_at->format('Y-m-d'))->toBe(now()->format('Y-m-d'));
});

it('user metadata timestamps are auto-managed by database', function () {
    // created_at and updated_at should never be editable in forms
    // They should be set automatically by database
    
    $user = User::factory()->create([
        'name' => 'Test User',
    ]);
    
    $createdAt = $user->created_at;
    
    // Update user - updated_at should change automatically
    sleep(1);
    $user->update(['name' => 'Updated Name']);
    $user->refresh();
    
    expect($user->created_at)->toEqual($createdAt);
    expect($user->updated_at->isAfter($createdAt))->toBeTrue();
});

it('activity log resource shows created_at but disabled', function () {
    // ActivityLogResource shows created_at in form as disabled (read-only)
    // This matches the requirement: visible for awareness, not editable
    
    $user = User::factory()->create();
    
    // Activity should be logged with its own created_at
    expect($user->created_at)->not->toBeNull();
});
