<?php

namespace App\Filament\Widgets\HR;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Contract;
use Carbon\Carbon;

class ContractExpirationAlertWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $activeContracts = Contract::where('status', 'active')->count();
        
        // Contratos vencendo nos próximos 30 dias
        $expiringIn30Days = Contract::whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->count();
        
        // Contratos urgentes (vencendo em 7 dias)
        $urgentContracts = Contract::whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();
        
        return [
            Stat::make('Contratos Ativos', $activeContracts)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Vencendo em 7 dias', $urgentContracts)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Vencendo em 30 dias', $expiringIn30Days)
                ->icon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
