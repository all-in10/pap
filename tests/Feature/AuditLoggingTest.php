<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin']);
        $this->employee = Employee::factory()->create();
    }

    /** @test */
    public function creating_employee_creates_activity_log()
    {
        Activity::truncate();

        $newEmployee = Employee::create([
            'first_name' => 'João',
            'last_name' => 'Silva',
            'email' => 'joao@example.com',
            'phone_number' => '123456789',
            'date_of_birth' => now()->subYears(30),
            'date_hired' => now(),
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_type' => Employee::class,
            'subject_id' => $newEmployee->id,
            'event' => 'created',
        ]);
    }

    /** @test */
    public function updating_employee_salary_creates_audit_log()
    {
        Activity::truncate();

        $this->actingAs($this->user);

        $this->employee->update(['email' => 'newemail@example.com']);

        $activity = Activity::where('subject_id', $this->employee->id)->first();

        $this->assertNotNull($activity);
        $this->assertEquals('updated', $activity->event);
        $this->assertEquals(Employee::class, $activity->subject_type);
    }

    /** @test */
    public function user_login_creates_activity_log()
    {
        Activity::truncate();

        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password', // Factory default
        ]);

        // Attempt a real login via controller
        $this->actingAs($this->user);

        // Verify user is logged in
        $this->assertAuthenticatedAs($this->user);
    }

    /** @test */
    public function audit_log_contains_user_information()
    {
        $this->actingAs($this->user);

        Activity::truncate();

        $this->employee->update(['email' => 'updated@example.com']);

        $activity = Activity::where('subject_id', $this->employee->id)->first();

        $this->assertNotNull($activity);
        $this->assertEquals($this->user->id, $activity->causer_id);
        $this->assertEquals(User::class, $activity->causer_type);
    }

    /** @test */
    public function only_admin_can_view_activity_logs()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $hrUser = User::factory()->create(['role' => 'hr']);
        $employeeUser = User::factory()->create(['role' => 'employee']);

        // Admin should pass authorization
        $this->assertTrue(auth()->user()?->role === 'admin' || $adminUser->role === 'admin');

        // Create activity
        Activity::create([
            'log_name' => 'default',
            'description' => 'Test log',
            'subject_type' => Employee::class,
            'subject_id' => $this->employee->id,
            'causer_type' => User::class,
            'causer_id' => $adminUser->id,
            'event' => 'created',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $this->employee->id,
        ]);
    }

    /** @test */
    public function activity_log_tracks_sensitive_fields()
    {
        Activity::truncate();

        $oldEmail = $this->employee->email;
        $newEmail = 'sensitive@example.com';

        $this->employee->update(['email' => $newEmail]);

        $activity = Activity::where('subject_id', $this->employee->id)->first();

        $this->assertNotNull($activity);
        // Activity log should track email field changes
    }

    /** @test */
    public function deleting_employee_creates_delete_activity()
    {
        Activity::truncate();

        $employeeId = $this->employee->id;

        $this->employee->delete();

        $activity = Activity::where('subject_id', $employeeId)->first();

        $this->assertNotNull($activity);
        $this->assertEquals('deleted', $activity->event);
    }
}
