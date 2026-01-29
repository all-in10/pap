<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\ChartWidget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HoursbankHistoryWidget extends ChartWidget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Histórico de Banco de Horas';
    
    protected static string $color = 'info';

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
            UserRole::EMPLOYEE,
            UserRole::HR,
        ];
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $employee = $user?->employee;

        if (!$employee) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Pega worklogs dos últimos 30 dias
        $worklogs = $employee->worklogs()
            ->where('work_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('work_date')
            ->get();

        // Agrupa por semana
        $weeks = $worklogs->groupBy(function ($worklog) {
            return $worklog->work_date->format('W-Y');
        });

        $labels = [];
        $extraHours = [];
        $regularHours = [];

        foreach ($weeks as $week => $logs) {
            $labels[] = 'Semana ' . explode('-', $week)[0];
            $extraHours[] = $logs->sum('extra_hours');
            $regularHours[] = $logs->sum('hours_worked');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Horas Extras',
                    'data' => $extraHours,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Horas Normais',
                    'data' => $regularHours,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
