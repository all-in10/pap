<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeInfoWidget extends Widget
{
    protected static ?string $heading = 'Informações do Funcionário';

    protected static string $view = 'filament.widgets.employee-info-widget';

    public function getEmployee(): ?Employee
    {
        return Auth::user()?->employee;
    }

    public function getTotalHoursWorked(): int
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return (int) $employee->worklogs()->sum('hours_worked');
    }

    public function getTotalExtraHours(): int
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return (int) $employee->worklogs()->sum('extra_hours');
    }

    public function getHoursbankBalance(): int
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return $employee->hoursbank?->total_hours ?? 0;
    }
}
