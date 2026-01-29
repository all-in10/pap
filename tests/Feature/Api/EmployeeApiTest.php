<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Enums\UserRole;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    protected User $adminUser;
    protected User $hrUser;
    protected Department $department;
    protected Designation $designation;
    protected Country $country;
    protected State $state;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        // Create country, state, city, department and designation
        $this->country = Country::factory()->create();
        $this->state = State::factory()->create(['country_id' => $this->country->id]);
        $this->city = City::factory()->create(['state_id' => $this->state->id]);
        $this->department = Department::factory()->create();
        $this->designation = Designation::factory()->create();

        // Create users
        $this->adminUser = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->hrUser = User::factory()->create(['role' => UserRole::HR]);
    }

    /** @test */
    public function can_get_all_employees_as_admin()
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/employees');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => ['id', 'first_name', 'last_name', 'email']
            ]
        ]);
    }

    /** @test */
    public function can_filter_employees_by_department()
    {
        $employee1 = Employee::factory()->create(['department_id' => $this->department->id]);
        $employee2 = Employee::factory()->create(['department_id' => Department::factory()->create()->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/v1/employees?department_id={$this->department->id}");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('total'));
    }

    /** @test */
    public function can_view_employee_details()
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->adminUser)
            ->getJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $employee->id]);
    }

    /** @test */
    public function can_update_employee()
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->hrUser)
            ->putJson("/api/v1/employees/{$employee->id}", [
                'first_name' => 'Maria',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('Maria', $employee->fresh()->first_name);
    }

    /** @test */
    public function can_delete_employee()
    {
        $employee = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->adminUser)
            ->deleteJson("/api/v1/employees/{$employee->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    /** @test */
    public function employee_role_cannot_manage_employees()
    {
        $employeeUser = User::factory()->create(['role' => UserRole::EMPLOYEE]);

        $response = $this->actingAs($employeeUser)
            ->getJson('/api/v1/employees');

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_api()
    {
        $response = $this->getJson('/api/v1/employees');

        $response->assertStatus(401);
    }
}
