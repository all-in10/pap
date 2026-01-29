<?php

namespace Tests\Feature\Notifications;

use App\Models\Timeoff;
use App\Models\Employee;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Tests\TestCase;

class TimeoffNotificationTest extends TestCase
{
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->employee = Employee::factory()->create();
    }

    public function test_notification_log_is_created_on_approval()
    {
        $timeoff = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'vacation',
            'status' => 'pending',
        ]);

        $timeoff->update(['status' => 'approved']);

        // Notification should be created by the listener
        $notification = NotificationLog::where('title', 'Solicitação de Folga Aprovada')
            ->where('user_id', $this->employee->user_id)
            ->first();

        $this->assertNotNull($notification);
        $this->assertFalse($notification->is_read);
        $this->assertStringContainsString('Férias', $notification->body);
    }

    public function test_notification_log_is_created_on_rejection()
    {
        $timeoff = Timeoff::create([
            'employee_id' => $this->employee->id,
            'start_date' => Carbon::parse('2026-02-01'),
            'end_date' => Carbon::parse('2026-02-05'),
            'type' => 'sick_leave',
            'status' => 'pending',
        ]);

        $timeoff->update(['status' => 'rejected']);

        // Notification should be created by the listener
        $notification = NotificationLog::where('title', 'Solicitação de Folga Rejeitada')
            ->where('user_id', $this->employee->user_id)
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Licença Médica', $notification->body);
    }
}
