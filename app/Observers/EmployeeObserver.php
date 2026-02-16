<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use App\Models\ContractType;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     *
     * @param  \App\Models\Employee  $employee
     * @return void
     */
    public function created(Employee $employee)
    {
        // Criar User automaticamente com email e nome do Employee
        try {
            // Verificar se User já existe com esse email
            $existingUser = User::where('email', $employee->email)->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'name' => trim($employee->first_name . ' ' . $employee->last_name),
                    'email' => $employee->email,
                    'password' => \Illuminate\Support\Facades\Hash::make(
                        env('DEFAULT_USER_PASSWORD', 'ChangeMe123!')
                    ),
                    'role' => 'employee',
                    'employee_id' => $employee->id,
                    'must_change_password' => true,
                ]);
                
                // Disparar evento ou guardar em sessão para notificação
                // Usa cache para passar dados entre Observer e Filament Resource
                cache()->put("employee_{$employee->id}_created_user", [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ], now()->addMinutes(5));
            }
        } catch (\Exception $e) {
            //\Log::error('Erro ao criar User para Employee: ' . $e->getMessage());
        }

        // Criar Contract automaticamente
        try {
            $baseSalary = $employee->designation ? $employee->designation->base_salary : 0;
            $contractType = ContractType::firstWhere('name', 'sem_termo');
            
            $contract = $employee->contracts()->create([
                'contract_type_id' => $contractType->id ?? null,
                'salary' => $baseSalary,
                'start_date' => $employee->date_hired,
                'date_hired' => $employee->date_hired,
                'status' => 'active',
            ]);
            
            // Guardar dados do Contract para notificação
            cache()->put("employee_{$employee->id}_created_contract", [
                'id' => $contract->id,
                'contract_type' => $contractType->name ?? 'N/A',
                'salary' => $baseSalary,
                'start_date' => $employee->date_hired?->format('d/m/Y'),
            ], now()->addMinutes(5));
        } catch (\Exception $e) {
            //\Log::error('Erro ao criar Contract para Employee: ' . $e->getMessage());
        }

        // Criar Hourbank automaticamente
        try {
            $hourbank = $employee->hourbanks()->create([
                'balance_hours' => 0,
                'last_accrual_date' => $employee->date_hired ?? now(),
            ]);
            
            // Guardar dados do Hourbank para notificação
            cache()->put("employee_{$employee->id}_created_hourbank", [
                'id' => $hourbank->id,
                'balance_hours' => $hourbank->balance_hours,
                'last_accrual_date' => $hourbank->last_accrual_date->format('d/m/Y'),
            ], now()->addMinutes(5));
        } catch (\Exception $e) {
            //\Log::error('Erro ao criar Hourbank para Employee: ' . $e->getMessage());
        }
    }
}
