<?php

namespace App\Filament\Employee\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'lg' => 3,
        ];
    }

    public function getViewData(): array
    {
        return [
            'employee' => Auth::user()?->employee,
        ];
    }
}
