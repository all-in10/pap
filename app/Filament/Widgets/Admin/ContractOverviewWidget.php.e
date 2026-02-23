<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Contract;
use Carbon\Carbon;

class ContractOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected ?string $heading = 'Visão Geral dos Contratos';

    protected function getStats(): array
    {
        $activeContracts = Contract::where('status', 'active')->count();
        $inactiveContracts = Contract::where('status', 'inactive')->count();
        
        // Contratos vencendo nos próximos 30 dias
        $expiringIn30Days = Contract::whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(30)])
            ->count();
        
        $totalContracts = Contract::count();
        
        return [
            Stat::make('Contratos Ativos', $activeContracts)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Contratos Inativos', $inactiveContracts)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
            
            /*Stat::make('Vencendo em 30 dias', $expiringIn30Days)
                ->description('Ação necessária')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),*/
        ];
    }
}
