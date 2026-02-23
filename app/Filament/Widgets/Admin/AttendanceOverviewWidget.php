<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\ChartWidget;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected ?string $heading = 'Visão Geral da Presença';

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
class AttendanceChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $maxContentWidth = 'full';

    public function getHeading(): string
    {
        $monthName = Carbon::now()->translatedFormat('F', null, 'pt_PT');
        return "Presença em {$monthName}";
    }

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $daysInMonth = Carbon::now()->daysInMonth;
        $totalEmployees = Employee::count();
        
        $labels = [];
        $presentData = [];
        $absentData = [];
        $lateData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create(Carbon::now()->year, Carbon::now()->month, $day);
            
            // Present = attendances recorded for that date
            $present = Attendance::whereDate('work_date', $date)->count();
            
            // Absent = employees without attendance record (total - present)
            $absent = $totalEmployees - $present;
            
            // Late = attendances with hours_worked greater than expected (8h)
            // For now, count records that might indicate late (can be refined)
            $late = 0; // Placeholder - need expected hours to determine

            $labels[] = $day;
            $presentData[] = $present;
            $absentData[] = max(0, $absent); // Ensure non-negative
            $lateData[] = $late;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Presentes',
                    'data' => $presentData,
                    'borderColor' => '#c2c5aa',
                    'backgroundColor' => 'rgba(194, 197, 170, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Ausentes',
                    'data' => $absentData,
                    'borderColor' => '#a68a64',
                    'backgroundColor' => 'rgba(166, 138, 100, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                ],
                /*[
                    'label' => 'Atrasados',
                    'data' => $lateData,
                    'borderColor' => '#b6ad90',
                    'backgroundColor' => 'rgba(182, 173, 144, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                    'fill' => true,
                ],*/
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true,
            'plugins' => [
                'filler' => [
                    'propagate' => true,
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 5,
                    ],
                ],
            ],
        ];
    }
}
