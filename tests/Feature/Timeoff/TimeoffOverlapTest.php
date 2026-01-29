<?php

namespace Tests\Feature\Timeoff;

use App\Models\Timeoff;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeoffOverlapTest extends TestCase
{
    use RefreshDatabase;

    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
    }

    public function test_cannot_create_overlapping_approved_timeoffs()
    {
        // Create first approved timeoff
        $timeoff1 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'vacation',
            'status' => 'approved',
        ]);

        // Try to create overlapping timeoff
        $this->expectException(\InvalidArgumentException::class);

        $timeoff2 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-03'),
            'end_date' => Carbon::parse('2026-02-10'),
            'type' => 'vacation',
            'status' => 'pending', // even if pending, will fail because we're checking approved ones
        ]);
    }

    public function test_can_create_pending_timeoffs_that_overlap()
    {
        // Multiple pending timeoffs can exist (for approval process)
        $timeoff1 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        // This should succeed since both are pending
        $timeoff2 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-03'),
            'end_date' => Carbon::parse('2026-02-10'),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('timeoffs', ['id' => $timeoff1->id]);
        $this->assertDatabaseHas('timeoffs', ['id' => $timeoff2->id]);
    }

    public function test_can_have_multiple_non_overlapping_timeoffs()
    {
        $timeoff1 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'vacation',
            'status' => 'approved',
        ]);

        $timeoff2 = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-03-01'),
            'end_date' => Carbon::parse('2026-03-10'),
            'type' => 'vacation',
            'status' => 'approved',
        ]);

        $this->assertCount(2, Timeoff::where('employee_id', $this->employee->id)->get());
    }
}
