<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AverageHoursWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $employee = $user?->employee;

        if (!$employee) {
            return [];
        }

        // Média de horas deste mês
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyAverage = $employee->worklogs()
            ->whereYear('work_date', $currentYear)
            ->whereMonth('work_date', $currentMonth)
            ->average('hours_worked') ?? 0;

        // Média geral (últimos 30 dias)
        $thirtyDaysAverage = $employee->worklogs()
            ->where('work_date', '>=', Carbon::now()->subDays(30))
            ->average('hours_worked') ?? 0;

        // Total de horas extras deste mês
        $monthlyExtraHours = $employee->worklogs()
            ->whereYear('work_date', $currentYear)
            ->whereMonth('work_date', $currentMonth)
            ->sum('extra_hours') ?? 0;

        return [
            Stat::make('Média de Horas (Mês Atual)', round($monthlyAverage, 2))
                ->description('Horas médias trabalhadas')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),
            
            Stat::make('Média de Horas (Últimos 30 dias)', round($thirtyDaysAverage, 2))
                ->description('Últimos 30 dias')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            
            Stat::make('Horas Extras (Mês Atual)', $monthlyExtraHours)
                ->description('Total de horas extras')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('warning'),
        ];
    }
}
