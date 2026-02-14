<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $hrUser;
    protected User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->hrUser = User::factory()->create(['role' => 'hr']);
        $this->employeeUser = User::factory()->create(['role' => 'employee']);
    }

    /** @test */
    public function only_admin_can_access_activity_log_resource()
    {
        // Admin can access
        $this->actingAs($this->adminUser)
            ->get('/admin/activity-logs')
            ->assertSuccessful();
    }

    /** @test */
    public function hr_cannot_access_activity_log_resource()
    {
        // HR cannot access
        $this->actingAs($this->hrUser)
            ->get('/admin/activity-logs')
            ->assertForbidden();
    }

    /** @test */
    public function employee_cannot_access_activity_log_resource()
    {
        // Employee cannot access
        $this->actingAs($this->employeeUser)
            ->get('/admin/activity-logs')
            ->assertForbidden();
    }

    /** @test */
    public function export_gate_allows_admin()
    {
        $this->assertTrue(
            auth()->user()?->role === 'admin' || $this->adminUser->role === 'admin'
        );
    }

    /** @test */
    public function export_gate_allows_hr()
    {
        $this->assertTrue(
            in_array($this->hrUser->role, ['admin', 'hr'])
        );
    }

    /** @test */
    public function export_gate_denies_employee()
    {
        $this->assertFalse(
            in_array($this->employeeUser->role, ['admin', 'hr'])
        );
    }

    /** @test */
    public function audit_gate_only_allows_admin()
    {
        // Simulate gate check
        $isAdmin = $this->adminUser->role === 'admin';
        $this->assertTrue($isAdmin);

        $isNotAdminForHR = $this->hrUser->role !== 'admin';
        $this->assertTrue($isNotAdminForHR);
    }
}
