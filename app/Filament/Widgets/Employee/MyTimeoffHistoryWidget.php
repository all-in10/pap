<?php

namespace App\Filament\Widgets\Employee;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Traits\EnforceEmployeeRole;

class MyTimeoffHistoryWidget extends BaseWidget
{
    use EnforceEmployeeRole;
    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

        public function getHeading(): ?string
    {
        return $this->isAuthenticatedAsEmployee() ? 'Histórico de Licenças' : null;
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

        // Estatísticas de licenças/férias
        $pendingTimeoffs = $employee->timeoffs()->pending()->count();
        $approvedTimeoffs = $employee->timeoffs()->approved()->count();
        $rejectedTimeoffs = $employee->timeoffs()->rejected()->count();
        
        return [
            Stat::make('Solicitações Pendentes', $pendingTimeoffs)
                ->icon('heroicon-o-clock')
                ->color('warning'),
            
            Stat::make('Aprovadas', $approvedTimeoffs)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Rejeitadas', $rejectedTimeoffs)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
