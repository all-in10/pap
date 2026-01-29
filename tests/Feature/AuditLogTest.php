<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\AuditLog;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_audit_log_on_model_create_with_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id]);

        // create a contract via factory to satisfy required fields
        $contract = Contract::factory()->create(['employee_id' => $employee->id]);

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => Contract::class,
            'model_id' => $contract->id,
            'action' => 'created',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_audit_log_on_model_update()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $contract = Contract::factory()->create(['employee_id' => $employee->id]);

        $contract->update(['salary' => 1500]);

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => Contract::class,
            'model_id' => $contract->id,
            'action' => 'updated',
            'user_id' => $user->id,
        ]);
    }

    public function test_creates_audit_log_on_model_delete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $contract = Contract::factory()->create(['employee_id' => $employee->id]);

        $contractId = $contract->id;
        $contract->delete();

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => Contract::class,
            'model_id' => $contractId,
            'action' => 'deleted',
            'user_id' => $user->id,
        ]);
    }
}
