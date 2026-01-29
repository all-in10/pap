<?php

namespace Tests\Unit\Models;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\Designation;
use App\Models\ContractType;
use Carbon\Carbon;
use Tests\TestCase;

class ContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Criar dados necessários
        if (!Designation::count()) {
            Designation::create([
                'name' => 'Test Designation',
                'base_salary' => 3000,
            ]);
        }

        if (!ContractType::count()) {
            ContractType::create([
                'name' => 'Tempo completo',
                'category' => 'full_time',
            ]);
        }
    }

    public function test_contract_requires_positive_salary()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Salary must be greater than zero');

        Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => -1000,
            'start_date' => now(),
            'status' => 'active',
            'date_hired' => now(),
        ]);
    }

    public function test_contract_requires_valid_dates()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Contract end date must be after start date');

        Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now()->addDay(),
            'end_date' => now(), // before start
            'status' => 'active',
            'date_hired' => now(),
        ]);
    }

    public function test_contract_requires_valid_status()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid contract status');

        Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now(),
            'status' => 'invalid_status',
            'date_hired' => now(),
        ]);
    }

    public function test_can_check_contract_status()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        $contract = Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now(),
            'status' => 'active',
            'date_hired' => now(),
        ]);

        $this->assertTrue($contract->isActive());
        $this->assertFalse($contract->isTerminated());
        $this->assertFalse($contract->isSuspended());
    }

    public function test_can_check_contract_within_period()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        // Contract valid today
        $contract = Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'date_hired' => now(),
        ]);

        $this->assertTrue($contract->isWithinPeriod());

        // Contract expired
        $expiredContract = Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDay(),
            'status' => 'active',
            'date_hired' => now(),
        ]);

        $this->assertFalse($expiredContract->isWithinPeriod());
    }

    public function test_soft_deletes_work()
    {
        $employee = Employee::factory()->create();
        $designation = Designation::first();
        $contractType = ContractType::first();

        $contract = Contract::create([
            'employee_id' => $employee->id,
            'designation_id' => $designation->id,
            'contract_type_id' => $contractType->id,
            'salary' => 3000,
            'start_date' => now(),
            'status' => 'active',
            'date_hired' => now(),
        ]);

        $contract->delete();

        $this->assertSoftDeleted($contract);
        // Soft delete hides from normal queries
        $this->assertEquals(0, Contract::where('id', $contract->id)->count());
        // But still visible with withTrashed()
        $this->assertEquals(1, Contract::withTrashed()->where('id', $contract->id)->count());
    }
}
