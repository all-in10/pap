<?php

namespace Tests\Unit\Models;

use App\Models\Timeoff;
use App\Models\Employee;
use Carbon\Carbon;
use Tests\TestCase;

class TimeoffTest extends TestCase
{
    public function test_timeoff_requires_valid_dates()
    {
        $employee = Employee::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Timeoff end date must be after start date');

        Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now()->addDay(),
            'end_date' => now(), // before start
            'type' => 'vacation',
            'status' => 'pending',
        ]);
    }

    public function test_timeoff_requires_valid_type()
    {
        $employee = Employee::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timeoff type');

        Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'invalid_type',
            'status' => 'pending',
        ]);
    }

    public function test_timeoff_requires_valid_status()
    {
        $employee = Employee::factory()->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timeoff status');

        Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'vacation',
            'status' => 'invalid_status',
        ]);
    }

    public function test_can_calculate_days_count()
    {
        $employee = Employee::factory()->create();

        $timeoff = Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $this->assertEquals(5, $timeoff->getDaysCount());
    }

    public function test_can_check_status_helpers()
    {
        $employee = Employee::factory()->create();

        $timeoff = Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $this->assertTrue($timeoff->isPending());
        $this->assertFalse($timeoff->isApproved());
        $this->assertFalse($timeoff->isRejected());

        $timeoff->update(['status' => 'approved']);
        $this->assertTrue($timeoff->isApproved());
    }

    public function test_can_get_type_label()
    {
        $employee = Employee::factory()->create();

        $vacation = Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $this->assertEquals('Férias', $vacation->getTypeLabel());

        $sick = Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(1),
            'type' => 'sick_leave',
            'status' => 'pending',
        ]);

        $this->assertEquals('Licença Médica', $sick->getTypeLabel());
    }

    public function test_soft_deletes_work()
    {
        $employee = Employee::factory()->create();

        $timeoff = Timeoff::create([
            'employee_id' => $employee->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $timeoff->delete();

        $this->assertSoftDeleted($timeoff);
        $this->assertCount(0, Timeoff::all());
        $this->assertCount(1, Timeoff::withTrashed()->get());
    }
}
