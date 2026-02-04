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

            ->discoverResources(in: app_path('Filament/HR/Resources'), for: 'App\\Filament\\HR\\Resources')
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
