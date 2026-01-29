<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\Widget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WorklogSummaryWidget extends Widget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Resumo de Ponto Recente';

    protected static string $view = 'filament.widgets.worklog-summary-widget';

    protected static function allowedRoles(): array
    {
        return [
            UserRole::EMPLOYEE,
        ];
    }

    public function getRecentWorklogs()
    {
        $user = Auth::user();
        $employee = $user?->employee;

        if (!$employee) {
            return [];
        }

        return $employee->worklogs()
            ->orderBy('work_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'work_date' => $log->work_date->format('d/m/Y'),
                'day_name' => $log->work_date->translatedFormat('l'),
                'start_time' => $log->start_time,
                'end_time' => $log->end_time,
                'hours_worked' => $log->hours_worked,
                'extra_hours' => $log->extra_hours,
            ]);
    }

    public function getCurrentMonthStats()
    {
        $user = Auth::user();
        $employee = $user?->employee;

        if (!$employee) {
            return [
                'total_days' => 0,
                'total_hours' => 0,
                'total_extra' => 0,
            ];
        }

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $worklogs = $employee->worklogs()
            ->whereYear('work_date', $currentYear)
            ->whereMonth('work_date', $currentMonth)
            ->get();

        return [
            'total_days' => $worklogs->count(),
            'total_hours' => (int) $worklogs->sum('hours_worked'),
            'total_extra' => (int) $worklogs->sum('extra_hours'),
        ];
    }
}
