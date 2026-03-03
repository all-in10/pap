<?php

namespace App\Filament\Widgets\Employee;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\EnforceEmployeeRole;

class MyAttendanceWidget extends BaseWidget
{
    use EnforceEmployeeRole;
    protected static ?string $heading = 'Minha Presença';
    public function getColumnSpan(): int | string | array

    {
        return 2;
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
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Registros de presença do mês
        $attendances = $employee->attendances()
            ->whereBetween('work_date', [$currentMonth, $currentMonthEnd])
            ->get();
        
        // Present days = days with attendance records
        $presentDays = $attendances->count();
        
        // Expected working days in month (approximate)
        $daysInMonth = Carbon::now()->daysInMonth;
        $absences = max(0, $daysInMonth - $presentDays);
        $lates = 0; // No late tracking without expected start time
        
        $attendanceRate = $daysInMonth > 0 ? round(($presentDays / $daysInMonth) * 100) : 0;
        
        return [
            Stat::make('Taxa de Presença', $attendanceRate . '%')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            
            Stat::make('Faltas', $absences)
                ->icon('heroicon-o-calendar')
                ->color('danger'),
            
            Stat::make('Atrasos', $lates)
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
