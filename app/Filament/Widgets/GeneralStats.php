<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\Timeoff;

class GeneralStats extends StatsOverviewWidget
{
    //protected static ?int $sort = 1;
    protected ?string $heading = 'Visão Geral';

    protected function getStats(): array
    {
        return [
            Stat::make('Utilizadores', User::count())->icon('heroicon-o-users'),
            //Stat::make('Colaboradores', Employee::count())->icon('heroicon-o-user-group'),
            Stat::make('Contratos ativos', Contract::where('status', 'active')->count())->icon('heroicon-o-document-text'),
            Stat::make('Licenças pendentes', Timeoff::where('status', 'pending')->count())->icon('heroicon-o-calendar'),
        ];
    }
}
