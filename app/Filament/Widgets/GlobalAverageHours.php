<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Worklog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalAverageHours extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $weeks = Worklog::min('work_date') && Worklog::max('work_date')
            ? ceil((strtotime(Worklog::max('work_date')) - strtotime(Worklog::min('work_date'))) / (7 * 24 * 60 * 60))
            : 1;
        $totalHours = Worklog::sum('hours_worked');
        $average = ($totalEmployees > 0 && $weeks > 0) ? round($totalHours / $totalEmployees / $weeks, 2) : 0;

        return [
            Stat::make('Média Semanal de Horas', $average)
                ->description('Horas médias por colaborador por semana')
                ->color('primary'),
        ];
    }
}
