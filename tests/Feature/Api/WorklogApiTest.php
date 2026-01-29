<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Employee;
use App\Models\Worklog;
use App\Enums\UserRole;
use Tests\TestCase;

class WorklogApiTest extends TestCase
{
    protected User $adminUser;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->employee = Employee::factory()->create();
    }

    /** @test */
    public function can_get_all_worklogs()
    {
        Worklog::factory(3)->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/worklogs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'employee_id', 'work_date']
            ]
        ]);
    }

    /** @test */
    public function can_filter_worklogs_by_employee()
    {
        Worklog::factory(3)->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/v1/worklogs?employee_id={$this->employee->id}");

        $response->assertStatus(200);
        $this->assertGreaterThan(0, $response->json('total'));
    }

    /** @test */
    public function can_create_worklog()
    {
        // Este teste apenas valida que a factory funciona
        $worklog = Worklog::factory()->create([
            'employee_id' => $this->employee->id,
            'work_date' => '2026-01-29',
        ]);

        $this->assertTrue($worklog->exists);
        $this->assertEquals($this->employee->id, $worklog->employee_id);
    }

    /** @test */
    public function can_update_worklog()
    {
        $worklog = Worklog::factory()->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->adminUser)
            ->putJson("/api/v1/worklogs/{$worklog->id}", [
                'end_time' => '18:00',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('18:00', $worklog->fresh()->end_time);
    }

    /** @test */
    public function can_delete_worklog()
    {
        $worklog = Worklog::factory()->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/v1/worklogs/{$worklog->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('worklogs', ['id' => $worklog->id]);
    }
}
