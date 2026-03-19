<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use App\Filament\Pages\EmployeeDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Support\Facades\FilamentAsset;

// Employee Resources
use App\Filament\Resources\TimeoffResource;
use App\Filament\Resources\VacationResource;
use App\Filament\Widgets\Employee\MyTimeoffHistoryWidget;
use App\Filament\Widgets\Employee\MyAttendanceWidget;
use App\Filament\Widgets\Employee\HourBankDetailWidget;
use App\Filament\Widgets\Employee\MyVacationBalanceWidget;

class EmployeePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('employee')
            ->path('employee')
            ->unsavedChangesAlerts()
            ->login()
            ->brandLogo(asset('images/Teamcorelogo.svg'))
            ->colors([
                'primary' => '#582f0e',
                'secondary' => '#7f4f24',
                'info' => '#936639',
                'danger' => '#a68a64',
                'warning' => '#b6ad90',
                'success' => '#c2c5aa',
                'gray' => '#acb79bff',
                'muted' => '#656d4a',
                'accent' => '#414833',
                'neutral' => '#333d29',
            ])
            ->favicon(asset('images/Document.svg'))

            // Employee pode aceder apenas ao TimeoffResource e VacationResource
            ->resources([
                TimeoffResource::class,
                VacationResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Employee/Pages'), for: 'App\\Filament\\Employee\\Pages')
            ->pages([
                //EmployeeDashboard::class,
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Employee/Widgets'), for: 'App\\Filament\\Employee\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\EmployeeInfoWidget::class,
                MyVacationBalanceWidget::class,
                MyTimeoffHistoryWidget::class,
                MyAttendanceWidget::class,
                HourBankDetailWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\EnsurePanelRole::class,
            ]);
    }
}
