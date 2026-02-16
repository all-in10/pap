<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\ChartWidget;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Presença no Mês Atual';
    protected static ?int $sort = 1;
    protected static ?string $maxContentWidth = 'full';

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
