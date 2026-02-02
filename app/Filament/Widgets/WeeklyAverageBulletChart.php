<?php

namespace App\Filament\Widgets;

use App\Models\Worklog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class WeeklyAverageBulletChart extends ChartWidget
{
    protected static ?string $heading = 'Média Semanal de Horas por Colaborador';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $firstDate = Worklog::min('work_date');
        $lastDate = Worklog::max('work_date');
        if (!$firstDate || !$lastDate) {
            return [
                'datasets' => [
                    [
                        'label' => 'Semanas',
                        'data' => [],
                        'pointStyle' => 'rectRot',
                        'pointRadius' => 6,
                        'backgroundColor' => '#7f4f24',
                        'borderColor' => '#7f4f24',
                    ],
                ],
                'labels' => [],
            ];
        }

        $start = Carbon::parse($firstDate)->startOfWeek();
        $end = Carbon::parse($lastDate)->endOfWeek();
        $weeks = [];
        $labels = [];
        $current = $start->copy();
        while ($current <= $end) {
            $weekStart = $current->copy();
            $weekEnd = $current->copy()->endOfWeek();
            $labels[] = 'Semana ' . $weekStart->diffInWeeks($start);
            $totalHours = Worklog::whereBetween('work_date', [$weekStart, $weekEnd])->sum('hours_worked');
            $employeeCount = Worklog::whereBetween('work_date', [$weekStart, $weekEnd])->distinct('employee_id')->count('employee_id');
            $weeks[] = $employeeCount > 0 ? round($totalHours / $employeeCount, 2) : 0;
            $current->addWeek();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Média semanal',
                    'data' => $weeks,
                    'pointStyle' => 'rectRot',
                    'pointRadius' => 6,
                    'backgroundColor' => '#7f4f24',
                    'borderColor' => '#7f4f24',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
