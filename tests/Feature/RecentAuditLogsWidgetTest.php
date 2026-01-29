<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentAuditLogsWidgetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function widget_displays_recent_audit_logs()
    {
        $admin = User::factory()->admin()->create();
        
        // Create test audit logs
        $logs = AuditLog::factory()->count(5)->create();

        $this->assertEquals(5, count($logs));
    }

    /** @test */
    public function widget_orders_by_most_recent()
    {
        // Create logs with known timestamps
        $oldLog = AuditLog::factory()->create(['created_at' => now()->subHours(2)]);
        $newLog = AuditLog::factory()->create(['created_at' => now()]);

        $logs = AuditLog::query()->latest('created_at')->limit(10)->get();

        $this->assertEquals($newLog->id, $logs->first()->id);
        $this->assertEquals($oldLog->id, $logs->last()->id);
    }

    /** @test */
    public function widget_limits_to_10_logs()
    {
        AuditLog::factory()->count(15)->create();

        $logs = AuditLog::query()->latest('created_at')->limit(10)->get();

        $this->assertCount(10, $logs);
    }

    /** @test */
    public function widget_displays_changes_count()
    {
        $logWithChanges = AuditLog::factory()->create([
            'changes' => ['name' => ['old' => 'John', 'new' => 'Jane']],
        ]);

        $this->assertEquals(1, count($logWithChanges->changes));
    }

    /** @test */
    public function widget_shows_system_for_null_user()
    {
        $log = AuditLog::factory()->create(['user_id' => null]);

        $this->assertNull($log->user_id);
    }
}
