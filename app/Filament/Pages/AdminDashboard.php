<?php

namespace App\Filament\Pages;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\Timeoff;

class AdminDashboard extends StatsOverviewWidget
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return (request()->segment(1) ?? '') === 'admin';
    }

    public function mount(): void
    {
        if ((request()->segment(1) ?? '') !== 'admin') {
            abort(403);
        }
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Utilizadores', User::count()),
            Stat::make('Colaboradores', Employee::count()),
            Stat::make('Contratos ativos', Contract::where('status', 'active')->count()),
            Stat::make('Licenças pendentes', Timeoff::where('status', 'pending')->count()),
        ];
    }
}
