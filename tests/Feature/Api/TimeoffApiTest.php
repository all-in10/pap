<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Employee;
use App\Models\Timeoff;
use App\Enums\UserRole;
use Tests\TestCase;

class TimeoffApiTest extends TestCase
{
    protected User $hrUser;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hrUser = User::factory()->create(['role' => UserRole::HR]);
        $this->employee = Employee::factory()->create();
    }

    /** @test */
    public function can_get_all_timeoffs()
    {
        // Criar timeoffs com datas diferentes para evitar overlaps
        Timeoff::factory()->create([
            'employee_id' => $this->employee->id,
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-05',
        ]);
        Timeoff::factory()->create([
            'employee_id' => $this->employee->id,
            'start_date' => '2026-02-10',
            'end_date' => '2026-02-15',
        ]);

        $response = $this->actingAs($this->hrUser)
            ->getJson('/api/v1/timeoffs');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'employee_id', 'type', 'status']
            ]
        ]);
    }

    /** @test */
    public function can_filter_timeoffs_by_status()
    {
        Timeoff::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'approved',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-05',
        ]);
        Timeoff::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'approved',
            'start_date' => '2026-02-10',
            'end_date' => '2026-02-15',
        ]);
        Timeoff::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'pending',
            'start_date' => '2026-02-20',
            'end_date' => '2026-02-25',
        ]);

        $response = $this->actingAs($this->hrUser)
            ->getJson('/api/v1/timeoffs?status=approved');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('total'));
    }

    /** @test */
    public function can_request_timeoff()
    {
        $response = $this->actingAs($this->hrUser)
            ->postJson('/api/v1/timeoffs', [
                'employee_id' => $this->employee->id,
                'type' => 'vacation',
                'start_date' => '2026-02-01',
                'end_date' => '2026-02-10',
                'reason' => 'Férias planejadas',
            ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['status' => 'pending']);
    }

    /** @test */
    public function can_approve_timeoff()
    {
        $timeoff = Timeoff::factory()->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->hrUser)
            ->putJson("/api/v1/timeoffs/{$timeoff->id}", [
                'status' => 'approved',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('approved', $timeoff->fresh()->status);
    }

    /** @test */
    public function can_delete_timeoff()
    {
        $timeoff = Timeoff::factory()->create(['employee_id' => $this->employee->id]);

        $response = $this->actingAs($this->hrUser)
            ->deleteJson("/api/v1/timeoffs/{$timeoff->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('timeoffs', ['id' => $timeoff->id]);
    }
}
