<?php

namespace App\Filament\Widgets\Employee;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Traits\EnforceEmployeeRole;

class HourBankDetailWidget extends BaseWidget
{
    use EnforceEmployeeRole;
    public function getColumnSpan(): int | string | array
    {
        return 1;
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
        
        // Obter saldo atual
        $currentBalance = $employee->hourbanks()
            ->latest()
            ->first();
        
        $balance = $currentBalance ? $currentBalance->balance_hours : 0;
        $balanceFormatted = number_format($balance, 2);
        
        // Total acumulado
        $totalAccumulated = $employee->hourbanks()
            ->sum('balance_hours') ?? 0;
        
        return [
            Stat::make('Saldo Atual', $balanceFormatted . 'h')
                ->icon('heroicon-o-clock')
                ->color($balance > 0 ? 'success' : ($balance < 0 ? 'danger' : 'info')),
            
            Stat::make('Total Acumulado', number_format($totalAccumulated, 2) . 'h')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),
            
            Stat::make('Última Atualização', $currentBalance?->last_accrual_date?->format('d/m/Y') ?? 'N/A')
                ->icon('heroicon-o-calendar')
                ->color('gray'),
        ];
    }
}
