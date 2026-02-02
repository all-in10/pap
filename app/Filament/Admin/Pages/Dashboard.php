<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ContractsChart;
use App\Filament\Widgets\TimeoffsChart;
use App\Filament\Widgets\DepartmentDistributionChart;
use App\Filament\Widgets\GlobalAverageHours;
use App\Filament\Widgets\WeeklyAverageBulletChart;
use Carbon\Traits\Week;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            WeeklyAverageBulletChart::class,
            ContractsChart::class,
            GlobalAverageHours::class,
            TimeoffsChart::class,
            DepartmentDistributionChart::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
        ];
    }
}