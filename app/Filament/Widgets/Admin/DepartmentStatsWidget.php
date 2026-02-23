<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\ChartWidget;
use App\Models\Department;
use App\Models\Employee;
use Filament\Support\Enums\Alignment;
use FontLib\Table\Type\loca;
use OpenSpout\Writer\XLSX\Options\PageOrientation;

class DepartmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Estatísticas dos Departamentos';

    protected function getStats(): array
    {
        $departments = Department::withCount('employees')->get();
        $topDepartment = $departments->sortByDesc('employees_count')->first();
        $totalEmployees = Employee::count();
        
        return [
            Stat::make('Total de Departamentos', Department::count())
                ->icon('heroicon-o-building-library')
                ->color('primary'),
            
            Stat::make('Total de Colaboradores', $totalEmployees)
                ->icon('heroicon-o-user-group')
                ->color('success'),
            
            Stat::make('Depto. Maior', $topDepartment?->name ?? 'N/A')
                ->description($topDepartment?->employees_count . ' colaboradores' ?? 'Sem dados')
                ->icon('heroicon-o-star')
                ->color('warning'),
        ];
    }
}
class DepartmentChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Colaboradores por Departamento';
    protected static ?int $sort = 2;
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
