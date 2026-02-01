<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Enums\UserRole;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_access_admin_panel_and_audit_is_recorded()
    {
        $user = User::factory()->create(['role' => UserRole::EMPLOYEE]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect('/employee');

        $this->assertDatabaseHas('audit_logs', ['event' => 'panel_access_denied', 'user_id' => $user->id]);
    }
}
