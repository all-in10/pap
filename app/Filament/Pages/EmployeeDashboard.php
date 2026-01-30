<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Employee;
use App\Models\Worklog;
use App\Models\Hoursbank;
use App\Models\Timeoff;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboard extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $navigationGroup = 'Dashboard';
    protected static string $view = 'filament.pages.employee-dashboard';
    // Register this page at the panel root so it is accessible directly at /employee
    // Setting this to '/' registers the page at the panel root (resulting URL: /employee)
    protected static ?string $slug = '/';
    protected static bool $shouldRegisterNavigation = true;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AverageHoursWidget::class,
            \App\Filament\Widgets\EmployeeInfoWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\WorklogSummaryWidget::class,
            \App\Filament\Widgets\HoursbankHistoryWidget::class,
            \App\Filament\Widgets\LicenseInformationWidget::class,
        ];
    }

    public function getWorklogData(): array
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return [];
        }

        return $employee
            ->worklogs()
            ->orderBy('work_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'work_date' => $log->work_date->format('Y-m-d'),
                'start_time' => $log->start_time,
                'end_time' => $log->end_time,
                'hours_worked' => $log->hours_worked,
                'extra_hours' => $log->extra_hours,
            ])
            ->toArray();
    }

    public function getDepartmentData(): ?string
    {
        $user = Auth::user();
        $employee = $user->employee;

        return $employee?->department?->name;
    }

    public function getHoursbankData(): ?int
    {
        $user = Auth::user();
        $employee = $user->employee;

        return $employee?->hoursbank?->total_hours ?? 0;
    }

    public function getTimeoffData(): array
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return [];
        }

        return $employee
            ->timeoffs()
            ->orderBy('start_date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($timeoff) => [
                'type' => $timeoff->type,
                'start_date' => $timeoff->start_date->format('Y-m-d'),
                'end_date' => $timeoff->end_date->format('Y-m-d'),
                'status' => $timeoff->status,
                'reason' => $timeoff->reason,
            ])
            ->toArray();
    }
}
