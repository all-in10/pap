<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RenewVacationBalances extends Command
{
    protected $signature = 'vacation:renew-balances';
    protected $description = 'Renova o saldo de férias para todos os employees no novo ano civil. Calcula dias proporcionais à data de contratação.';

    public function handle(): int
    {
        $this->info('Iniciando renovação de saldos de férias...');

        $currentYear = now()->year;
        $employees = Employee::where('is_active', true)->get();

        $renewed = 0;
        $skipped = 0;

        foreach ($employees as $employee) {
            // Se o saldo já é do ano atual, não faz nada
            if ($employee->vacation_year == $currentYear) {
                $skipped++;
                continue;
            }

            // Calcula dias proporcionais baseado na data de contratação
            // Se contratado em junho (6 meses), recebe: 22 × (7 meses restantes / 12) ≈ 13 dias
            $hireDate = $employee->date_hired ? Carbon::parse($employee->date_hired) : now();

            // Se já passou do 1º de janeiro desse ano (renovação automática para anos futuros)
            if ($hireDate->year == $currentYear) {
                // Employee contratado esse ano: calcula proporção desde contratação até 31 dez
                $monthsRemaining = Carbon::parse($hireDate)->diffInMonths(Carbon::parse("{$currentYear}-12-31"));
                $proportionalDays = (int) ceil((22 * ($monthsRemaining + 1)) / 12);
            } else {
                // Employee contratado em anos anteriores: recebe saldo completo
                $proportionalDays = 22;
            }

            // Atualiza saldo
            $employee->update([
                'vacation_balance' => $proportionalDays,
                'vacation_year' => $currentYear,
                'last_balance_renewal_at' => now(),
            ]);

            $renewed++;
            $this->line("✓ {$employee->first_name} {$employee->last_name}: {$proportionalDays} dias");
        }

        $this->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Renovação concluída!");
        $this->info("✓ Renovados: {$renewed}");
        $this->info("⊘ Pulados (já renovados): {$skipped}");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return self::SUCCESS;
    }
}
