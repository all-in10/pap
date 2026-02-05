<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
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

// HR Resources
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\ContractResource;
use App\Filament\Resources\DepartmentResource;
use App\Filament\Resources\DesignationResource;
use App\Filament\Resources\TimeoffResource;
use App\Filament\Resources\TimeoffCategoryResource;
use App\Filament\Resources\BenefitResource;
use App\Filament\Resources\WorklogResource;
use App\Filament\Resources\HourbankResource;
use App\Filament\Resources\AttendanceResource;

class HRPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
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

            // HR pode aceder apenas a estes resources
            ->resources([
                EmployeeResource::class,
                ContractResource::class,
                DepartmentResource::class,
                DesignationResource::class,
                TimeoffResource::class,
                TimeoffCategoryResource::class,
                BenefitResource::class,
                WorklogResource::class,
                HourbankResource::class,
                AttendanceResource::class,
            ])
            ->discoverPages(in: app_path('Filament/HR/Pages'), for: 'App\\Filament\\HR\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/HR/Widgets'), for: 'App\\Filament\\HR\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
                \App\Filament\Widgets\GeneralStats::class,
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
