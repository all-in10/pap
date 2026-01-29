<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use App\Services\DashboardStatisticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverviewWidget extends BaseWidget
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
            Stat::make('Total de Funcionários', DashboardStatisticsService::getTotalEmployees())
                ->description('Ativos no sistema')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->icon('heroicon-o-user-group'),

            Stat::make('Contratos Ativos', DashboardStatisticsService::getActiveContracts())
                ->description('Vigentes e válidos')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('success')
                ->icon('heroicon-o-document-check'),

            Stat::make('Contratos Vencendo', DashboardStatisticsService::getExpiringContractsSoon())
                ->description('Próximos 30 dias')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Solicitações Pendentes', DashboardStatisticsService::getPendingTimeoffs())
                ->description('Aguardando aprovação')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger')
                ->icon('heroicon-o-clock'),

            Stat::make('Horas Registradas Hoje', number_format(DashboardStatisticsService::getTodayHours(), 2) . 'h')
                ->description('Total de horas')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->icon('heroicon-o-calendar'),

            Stat::make('Taxa de Rotatividade', number_format(DashboardStatisticsService::getTurnoverRate(), 2) . '%')
                ->description('Este mês')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('info')
                ->icon('heroicon-o-arrow-trending-down'),
        ];
    }
}
