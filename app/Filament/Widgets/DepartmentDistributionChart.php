<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use Filament\Widgets\ChartWidget;

class DepartmentDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Pessoal por Departamento';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $departments = Department::withCount('employees')->get();
        $colors = [
            'rgba(245, 158, 11, 0.8)', // amber
            'rgba(16, 185, 129, 0.8)', // green
            'rgba(239, 68, 68, 0.8)',  // red
            'rgba(59, 130, 246, 0.8)', // blue
            'rgba(139, 92, 246, 0.8)', // purple
            'rgba(251, 191, 36, 0.8)', // yellow
            'rgba(34, 197, 94, 0.8)',  // emerald
            'rgba(236, 72, 153, 0.8)', // pink
        ];
        $borderColors = [
            'rgb(245, 158, 11)',
            'rgb(16, 185, 129)',
            'rgb(239, 68, 68)',
            'rgb(59, 130, 246)',
            'rgb(139, 92, 246)',
            'rgb(251, 191, 36)',
            'rgb(34, 197, 94)',
            'rgb(236, 72, 153)',
        ];

        $data = $departments->pluck('employees_count')->toArray();
        $labels = $departments->pluck('name')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Nº de Colaboradores',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
