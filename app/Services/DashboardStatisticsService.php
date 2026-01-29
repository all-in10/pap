<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\Timeoff;
use App\Models\Worklog;
use Carbon\Carbon;

class DashboardStatisticsService
{
    /**
     * Get total employees count
     */
    public static function getTotalEmployees(): int
    {
        return Employee::count();
    }

    /**
     * Get active contracts count
     */
    public static function getActiveContracts(): int
    {
        return Contract::active()->count();
    }

    /**
     * Get expiring contracts in 30 days
     */
    public static function getExpiringContractsSoon(): int
    {
        return Contract::whereBetween('end_date', [now(), now()->addDays(30)])
            ->where('status', '!=', 'terminated')
            ->count();
    }

    /**
     * Get pending timeoff requests
     */
    public static function getPendingTimeoffs(): int
    {
        return Timeoff::where('status', 'pending')->count();
    }

    /**
     * Get total hours logged today
     */
    public static function getTodayHours(): float
    {
        return Worklog::whereDate('work_date', today())
            ->selectRaw('SUM(hours_worked) as total_hours')
            ->value('total_hours') ?? 0;
    }

    /**
     * Get monthly timeoff summary
     */
    public static function getMonthlyTimeoffSummary(): array
    {
        return Timeoff::where('status', 'approved')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get()
            ->toArray();
    }

    /**
     * Get employee turnover rate
     */
    public static function getTurnoverRate(): float
    {
        $totalEmployees = Employee::count();
        if ($totalEmployees === 0) {
            return 0;
        }

        $terminatedThisMonth = Contract::where('status', 'terminated')
            ->whereMonth('end_date', now()->month)
            ->count();

        return ($terminatedThisMonth / $totalEmployees) * 100;
    }

    /**
     * Get department distribution
     */
    public static function getDepartmentDistribution(): array
    {
        return Employee::selectRaw('department_id, COUNT(*) as count')
            ->with('department')
            ->groupBy('department_id')
            ->get()
            ->map(fn($item) => [
                'name' => $item->department?->name ?? 'Não Definido',
                'count' => $item->count,
            ])
            ->toArray();
    }

    /**
     * Get average contract salary by department
     */
    public static function getAverageSalaryByDepartment(): array
    {
        return Contract::active()
            ->selectRaw('employees.department_id, AVG(contracts.salary) as average_salary')
            ->join('employees', 'contracts.employee_id', '=', 'employees.id')
            ->groupBy('employees.department_id')
            ->with(['employee.department'])
            ->get()
            ->map(fn($contract) => [
                'department' => $contract->employee?->department?->name ?? 'Não Definido',
                'average_salary' => round($contract->average_salary, 2),
            ])
            ->toArray();
    }

    /**
     * Get worklog summary this week
     */
    public static function getWeeklyWorklogSummary(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return Worklog::whereBetween('work_date', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(work_date) as date, SUM(hours_worked) as hours')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => [
                'date' => Carbon::parse($item->date)->format('d/m'),
                'hours' => round($item->hours, 2),
            ])
            ->toArray();
    }

    /**
     * Get recent approvals
     */
    public static function getRecentApprovals($limit = 10): array
    {
        return Timeoff::where('status', 'approved')
            ->latest('updated_at')
            ->with('employee')
            ->limit($limit)
            ->get()
            ->map(fn($timeoff) => [
                'employee' => $timeoff->employee->name ?? 'N/A',
                'type' => $timeoff->getTypeLabel(),
                'days' => $timeoff->days_count,
                'approved_at' => $timeoff->updated_at->format('d/m/Y H:i'),
            ])
            ->toArray();
    }
}
