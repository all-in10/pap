<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use App\Services\DashboardStatisticsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class WeeklyWorklogChart extends ChartWidget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Horas Registradas (Esta Semana)';

    protected int | string | array $columnSpan = 'half';

    protected static ?int $sort = 4;

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
            UserRole::EMPLOYEE,
            //UserRole::HR,
        ];
    }

    protected function getData(): array
    {
        $data = Cache::remember('dashboard.weekly_worklog', 3600, function () {
            return DashboardStatisticsService::getWeeklyWorklogSummary();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Horas',
                    'data' => array_column($data, 'hours'),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => '#3B82F640',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_column($data, 'date'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
