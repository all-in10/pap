<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;

class HRDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.hr-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return (request()->segment(1) ?? '') === 'hr';
    }

    public function mount(): void
    {
        if ((request()->segment(1) ?? '') !== 'hr') {
            abort(403);
        }
    }
}