<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users()
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        
        $this->assertTrue(
            $admin->can('viewAny', User::class),
            'Admin deve poder visualizar utilizadores'
        );
    }

    public function test_hr_cannot_view_users()
    {
        $hr = User::factory()->create(['role' => 'HR']);
        
        $this->assertFalse(
            $hr->can('viewAny', User::class),
            'HR não deve poder visualizar utilizadores'
        );
    }

    public function test_admin_can_view_countries()
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        
        $this->assertTrue(
            $admin->can('viewAny', \App\Models\Country::class),
            'Admin deve poder visualizar países'
        );
    }

    public function test_hr_cannot_view_countries()
    {
        $hr = User::factory()->create(['role' => 'HR']);
        
        $this->assertFalse(
            $hr->can('viewAny', \App\Models\Country::class),
            'HR não deve poder visualizar países'
        );
    }

    public function test_admin_can_view_states()
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        
        $this->assertTrue(
            $admin->can('viewAny', \App\Models\State::class),
            'Admin deve poder visualizar estados'
        );
    }

    public function test_hr_cannot_view_states()
    {
        $hr = User::factory()->create(['role' => 'HR']);
        
        $this->assertFalse(
            $hr->can('viewAny', \App\Models\State::class),
            'HR não deve poder visualizar estados'
        );
    }

    public function test_admin_can_view_cities()
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        
        $this->assertTrue(
            $admin->can('viewAny', \App\Models\City::class),
            'Admin deve poder visualizar cidades'
        );
    }

    public function test_hr_cannot_view_cities()
    {
        $hr = User::factory()->create(['role' => 'HR']);
        
        $this->assertFalse(
            $hr->can('viewAny', \App\Models\City::class),
            'HR não deve poder visualizar cidades'
        );
    }

    public function test_admin_can_view_contract_types()
    {
        $admin = User::factory()->create(['role' => 'ADMIN']);
        
        $this->assertTrue(
            $admin->can('viewAny', \App\Models\ContractType::class),
            'Admin deve poder visualizar tipos de contrato'
        );
    }

    public function test_hr_cannot_view_contract_types()
    {
        $hr = User::factory()->create(['role' => 'HR']);
        
        $this->assertFalse(
            $hr->can('viewAny', \App\Models\ContractType::class),
            'HR não deve poder visualizar tipos de contrato'
        );
    }
}
