<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Traits\EnforceEmployeeRole;
use Carbon\Carbon;

class EmployeeInfoWidget extends BaseWidget
{
    use EnforceEmployeeRole;

    protected ?string $heading = 'Minhas Informações';
    protected static ?int $sort = -1;

    public function getColumnSpan(): int | string | array
    {
        return 3;
    }

    protected function getStats(): array
    {
        // Verificar se o usuário é employee
        if (!$this->isAuthenticatedAsEmployee()) {
            return [];
        }

        $user = Auth::user();

        if (!$user || !$user->employee_id) {
            return [
                Stat::make('Erro', 'Funcionário não encontrado')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $employee = $user->employee;

        // Informações pessoais
        $fullName = "{$employee->first_name} {$employee->last_name}";
        $department = $employee->department?->name ?? 'N/A';
        $designation = $employee->designation?->name ?? 'N/A';
        $hiredDate = $employee->date_hired
            ? Carbon::parse($employee->date_hired)->format('d/m/Y')
            : 'N/A';

        return [
            Stat::make('Nome Completo', $fullName)
                ->icon('heroicon-o-user')
                ->color('primary'),

            Stat::make('Cargo', $designation)
                ->icon('heroicon-o-briefcase')
                ->color('info'),

            Stat::make('Departamento', $department)
                ->icon('heroicon-o-building-office')
                ->color('success'),

            Stat::make('Data de Admissão', $hiredDate)
                ->icon('heroicon-o-calendar')
                ->color('warning'),
        ];
    }
}
