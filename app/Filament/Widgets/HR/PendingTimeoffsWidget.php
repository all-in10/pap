<?php

namespace App\Filament\Widgets\HR;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Timeoff;
use Carbon\Carbon;

class PendingTimeoffsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingCount = Timeoff::where('status', 'pending')->count();
        $approvedCount = Timeoff::where('status', 'approved')->count();
        $rejectedCount = Timeoff::where('status', 'rejected')->count();
        
        // Próximas férias aprovadas
        $upcomingTimeoffs = Timeoff::where('status', 'approved')
            ->where('start_date', '>=', today())
            ->count();
        
        return [
            Stat::make('Solicitações Pendentes', $pendingCount)
                ->icon('heroicon-o-clock')
                ->color('warning'),
            
            Stat::make('Aprovadas', $approvedCount)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Rejeitadas', $rejectedCount)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
