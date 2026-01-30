<?php

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sets intended url to employee portal on login when none is set', function () {
    $user = User::factory()->create(['role' => 'employee']);

    // Ensure no intended url exists
    expect(session('url.intended'))->toBeNull();

    // Fire the Login event which our listener handles
    event(new Login('web', $user, false));

    expect(session('url.intended'))->toBe(url('/employee'));
});

it('does not override an existing intended url on login', function () {
    $user = User::factory()->create(['role' => 'admin']);

    // Pre-set an intended URL that points to a protected admin subpage
    session(['url.intended' => url('/admin/some-page')]);

    // Fire the Login event
    event(new Login('web', $user, false));

    // The intended URL should remain unchanged
    expect(session('url.intended'))->toBe(url('/admin/some-page'));
});

it('employee panel root serves dashboard at /employee without redirect', function () {
    $user = User::factory()->create(['role' => 'employee']);

    /** @var \Tests\TestCase $this */

    // Direct access to the canonical dashboard route should work
    $dashboard = $this->actingAs($user)->get('/employee/employee-dashboard');
    // Debug contents
    $dashboard->dump();
    $dashboard->assertOk();
    $dashboard->assertSeeText('Férias');

    $response = $this->actingAs($user)->get('/employee');

    // Should return OK and render the dashboard content while keeping URL /employee
    $response->assertOk();
    $response->assertDontSeeText('Redirecting');
    $response->assertSeeText('Férias');
});
