<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class EmployeeDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.employee-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return (request()->segment(1) ?? '') === 'employee';
    }

    public function mount(): void
    {
        if ((request()->segment(1) ?? '') !== 'employee') {
            abort(403);
        }
    }
}