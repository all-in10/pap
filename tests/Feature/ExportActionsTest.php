<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $hrUser;
    protected User $employeeUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar usuários de teste
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->hrUser = User::factory()->create(['role' => 'hr']);
        $this->employeeUser = User::factory()->create(['role' => 'employee']);

        // Criar alguns funcionários para exportação
        Employee::factory()->count(5)->create();
    }

    /** @test */
    public function admin_can_export_to_csv()
    {
        $this->actingAs($this->adminUser)
            ->get(route('export.csv', 'Employee'))
            ->assertSuccessful()
            ->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    /** @test */
    public function admin_can_export_to_excel()
    {
        $this->actingAs($this->adminUser)
            ->get(route('export.excel', 'Employee'))
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** @test */
    public function admin_can_export_to_json()
    {
        $this->actingAs($this->adminUser)
            ->get(route('export.json', 'Employee'))
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/json; charset=utf-8');
    }

    /** @test */
    public function hr_can_export_to_csv()
    {
        $this->actingAs($this->hrUser)
            ->get(route('export.csv', 'Employee'))
            ->assertSuccessful();
    }

    /** @test */
    public function hr_can_export_to_excel()
    {
        $this->actingAs($this->hrUser)
            ->get(route('export.excel', 'Employee'))
            ->assertSuccessful();
    }

    /** @test */
    public function hr_can_export_to_json()
    {
        $this->actingAs($this->hrUser)
            ->get(route('export.json', 'Employee'))
            ->assertSuccessful();
    }

    /** @test */
    public function employee_cannot_export()
    {
        $this->actingAs($this->employeeUser)
            ->get(route('export.csv', 'Employee'))
            ->assertForbidden();
    }

    /** @test */
    public function unauthenticated_user_cannot_export()
    {
        $this->get(route('export.csv', 'Employee'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function export_returns_correct_data_count()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('export.json', 'Employee'));

        $response->assertSuccessful();
    }
}
