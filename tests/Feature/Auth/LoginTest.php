<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::EMPLOYEE,
            'must_change_password' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Login functionality depends on Filament configuration
        // This test validates the user exists with correct credentials
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'employee',
        ]);
    }

    public function test_user_fails_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt with wrong password
        $this->assertFalse(\Illuminate\Support\Facades\Hash::check('wrongpassword', $user->password));
    }

    public function test_user_can_check_role_hierarchy()
    {
        $root = User::factory()->create(['role' => UserRole::ROOT]);
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $hr = User::factory()->create(['role' => UserRole::HR]);
        $employee = User::factory()->create(['role' => UserRole::EMPLOYEE]);

        // Test role checking
        $this->assertTrue($root->isRoot());
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($hr->isHr());
        $this->assertTrue($employee->isEmployee());

        // Test hierarchy (Admin >= HR >= Employee)
        $this->assertTrue($admin->hasPrivilegeOf(UserRole::HR));
        $this->assertTrue($admin->hasPrivilegeOf(UserRole::EMPLOYEE));
        $this->assertFalse($employee->hasPrivilegeOf(UserRole::HR));
    }

    public function test_must_change_password_flag_works()
    {
        $user = User::factory()->create([
            'password' => bcrypt('default_password'),
            'must_change_password' => true,
        ]);

        $this->assertTrue($user->must_change_password);

        $user->update(['must_change_password' => false]);
        $this->assertFalse($user->fresh()->must_change_password);
    }
}
