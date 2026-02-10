<?php

namespace App\Filament\Widgets\HR;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Employee;

class EmployeeDirectoryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('is_active', true)->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;
        
        return [
            Stat::make('Total de Colaboradores', $totalEmployees)
                ->icon('heroicon-o-user-group')
                ->color('primary'),
            
            Stat::make('Colaboradores Ativos', $activeEmployees)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('Colaboradores Inativos', $inactiveEmployees)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
