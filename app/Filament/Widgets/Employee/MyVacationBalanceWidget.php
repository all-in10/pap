<?php

namespace App\Filament\Widgets\Employee;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Traits\EnforceEmployeeRole;
use App\Models\Vacation;

class MyVacationBalanceWidget extends BaseWidget
{
    use EnforceEmployeeRole;

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

    public function getHeading(): ?string
    {
        return $this->isAuthenticatedAsEmployee() ? 'Saldo de Férias' : null;
    }

    protected function getStats(): array
    {
        // Verificar se o usuário é employee
        if (!$this->isAuthenticatedAsEmployee()) {
            return [];
        }

        $user = Auth::user();

        if (!$user || !$user->employee_id) {
            return [
                Stat::make('Erro', 'Funcionário não encontrado')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $employee = $user->employee;
        $currentYear = now()->year;

        // Dias restantes
        $balance = $employee->vacation_balance ?? 0;

        // Férias aprovadas este ano
        $approvedDays = $employee->vacations()
            ->where('vacation_year', $currentYear)
            ->approved()
            ->sum('days_taken') ?? 0;

        // Férias pendentes este ano
        $pendingDays = $employee->vacations()
            ->where('vacation_year', $currentYear)
            ->pending()
            ->sum('days_taken') ?? 0;

        // Última renovação
        $lastRenewal = $employee->last_balance_renewal_at?->format('d/m/Y') ?? 'Nunca';

        return [
            Stat::make('Dias Disponíveis', $balance)
                ->icon('heroicon-o-calendar')
                ->color($balance > 0 ? 'success' : ($balance < 0 ? 'danger' : 'warning')),

            Stat::make('Dias Aprovados', $approvedDays)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Dias Pendentes', $pendingDays)
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Última Renovação', $lastRenewal)
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
        ];
    }
}
