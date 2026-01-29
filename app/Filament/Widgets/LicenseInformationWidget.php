<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\Widget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class LicenseInformationWidget extends Widget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Informações sobre Licenças';

    protected static string $view = 'filament.widgets.license-information-widget';

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ROOT,
            UserRole::ADMIN,
        ];
    }

    public function getEmployee(): ?Employee
    {
        return Auth::user()?->employee;
    }

    public function getPendingLicenses()
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return $employee->timeoffs()
            ->where('type', 'license')
            ->where('status', 'pending')
            ->count();
    }

    public function getApprovedLicenses()
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return $employee->timeoffs()
            ->where('type', 'license')
            ->where('status', 'approved')
            ->count();
    }

    public function getTotalLicenses()
    {
        $employee = $this->getEmployee();
        
        if (!$employee) {
            return 0;
        }

        return $employee->timeoffs()
            ->where('type', 'license')
            ->count();
    }
}
