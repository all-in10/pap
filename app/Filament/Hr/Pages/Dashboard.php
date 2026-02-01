<?php

namespace App\Filament\Hr\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ContractsChart;
use App\Filament\Widgets\TimeoffsChart;
use App\Filament\Widgets\PendingTimeoffs;
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            PendingTimeoffs::class,
            TimeoffsChart::class,
            ContractsChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 3;
    }
}