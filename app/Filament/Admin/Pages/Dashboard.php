<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ContractsChart;
use App\Filament\Widgets\TimeoffsChart;
use Filament\Facades\Filament;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public static function getNavigationUrl(): string
    {
        // Dashboard should point to the panel root instead of a separate page route
        return Filament::getUrl();
    }

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