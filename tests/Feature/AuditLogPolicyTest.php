<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AuditLog;

class AuditLogPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_privileged_user_cannot_access_audit_logs()
    {
        $user = User::factory()->create(['role' => 'employee']);
        $this->actingAs($user);

        $response = $this->getJson('/api/audit-logs');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_audit_logs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->getJson('/api/audit-logs');
        $response->assertStatus(200);
    }
}
