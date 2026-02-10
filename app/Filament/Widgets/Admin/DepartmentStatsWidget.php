<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Department;
use App\Models\Employee;

class DepartmentStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $departments = Department::withCount('employees')->get();
        $topDepartment = $departments->sortByDesc('employees_count')->first();
        $totalEmployees = Employee::count();
        
        return [
            Stat::make('Total de Departamentos', Department::count())
                ->icon('heroicon-o-building-library')
                ->color('primary'),
            
            Stat::make('Total de Colaboradores', $totalEmployees)
                ->icon('heroicon-o-user-group')
                ->color('success'),
            
            Stat::make('Depto. Maior', $topDepartment?->name ?? 'N/A')
                ->description($topDepartment?->employees_count . ' colaboradores' ?? 'Sem dados')
                ->icon('heroicon-o-star')
                ->color('warning'),
        ];
    }
}
