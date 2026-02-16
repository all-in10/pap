<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\ChartWidget;
use App\Models\Department;
use Illuminate\Support\Collection;

class DepartmentChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Colaboradores por Departamento';
    protected static ?int $sort = 1;
    protected static ?string $maxContentWidth = 'full';

    protected function getData(): array
    {
        $departments = Department::withCount('employees')
            ->orderByDesc('employees_count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Colaboradores',
                    'data' => $departments->pluck('employees_count')->toArray(),
                    'backgroundColor' => [
                        '#582f0e',
                        '#7f4f24',
                        '#936639',
                        '#a68a64',
                        '#b6ad90',
                        '#c2c5aa',
                        '#acb79b',
                        '#656d4a',
                        '#414833',
                        '#333d29',
                    ],
                    'borderColor' => '#582f0e',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $departments->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
