<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Registros do mês atual
        $currentMonthAttendances = Attendance::whereBetween('work_date', [$currentMonth, $currentMonthEnd])->get();
        $totalEmployees = Employee::count();
        
        // Taxa de presença
        // Present days = unique dates with attendance records
        $presentDays = $currentMonthAttendances->unique('work_date')->count();
        
        // Expected working days (approximate: employees × days in month)
        $daysInMonth = Carbon::now()->daysInMonth;
        $totalExpectedDays = $totalEmployees * $daysInMonth;
        $attendanceRate = $totalExpectedDays > 0 ? round(($presentDays / $daysInMonth) * 100) : 0;
        
        // Faltas no mês (estimated as total expected - actual attendances)
        $absences = max(0, $totalExpectedDays - $currentMonthAttendances->count());
        
        // Média de horas trabalhadas
        $totalHours = $currentMonthAttendances->sum('hours_worked') ?? 0;
        $avgHours = $totalExpectedDays > 0 ? round($totalHours / $totalExpectedDays, 2) : 0;
        
        return [
            Stat::make('Taxa de Presença', $attendanceRate . '%')
                ->icon('heroicon-o-check-badge')
                ->color('success'),
            
            Stat::make('Faltas (Mês Atual)', $absences)
                ->icon('heroicon-o-calendar-days')
                ->color('danger'),
            
            Stat::make('Média de Horas', $avgHours . 'h')
                ->description('Por dia trabalhado')
                ->icon('heroicon-o-clock')
                ->color('info'),
        ];
    }
}
