<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ContractsChart;
use App\Filament\Widgets\TimeoffsChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            ContractsChart::class,
            TimeoffsChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}