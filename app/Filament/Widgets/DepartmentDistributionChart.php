<?php

namespace App\Filament\Widgets;

use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class DepartmentDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribuição por Departamento';

    protected int | string | array $columnSpan = 'half';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Cache::remember('dashboard.department_distribution', 3600, function () {
            return DashboardStatisticsService::getDepartmentDistribution();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Funcionários',
                    'data' => array_column($data, 'count'),
                    'backgroundColor' => [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#EC4899',
                        '#14B8A6',
                        '#F97316',
                    ],
                    'borderColor' => '#fff',
                ],
            ],
            'labels' => array_column($data, 'name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
