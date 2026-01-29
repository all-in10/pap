<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use WidgetVisibility;

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
        ];
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuários', \App\Models\User::count())
                ->description('Usuários registrados no sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color(Color::hex('#3b82f6')), // blue-500
            Stat::make('Total de Funcionários', \App\Models\Employee::count())
                ->description('Funcionários ativos')
                ->descriptionIcon('heroicon-m-user-group')
                ->color(Color::hex('#10b981')), // emerald-500
            Stat::make('Contratos Ativos', \App\Models\Contract::where('status', 'active')->count())
                ->description('Contratos em vigor')
                ->descriptionIcon('heroicon-m-document-text')
                ->color(Color::hex('#f59e0b')), // amber-500
            Stat::make('Férias Pendentes', \App\Models\Timeoff::where('status', 'pending')->count())
                ->description('Solicitações aguardando aprovação')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color(Color::hex('#ef4444')), // red-500
        ];
    }
}