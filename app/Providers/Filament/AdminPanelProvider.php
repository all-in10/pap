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
use Filament\Navigation\MenuItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Settings;
use App\Http\Middleware\ForcePasswordChange;
use App\Http\Middleware\ValidateUserPanelRole;
use App\Http\Middleware\EnsureAdminPanelAccess; 

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandLogo(asset('Background@3x.svg'))
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
            ->userMenuItems([
                MenuItem::make()
                    ->label('Configurações')
                    ->url(fn (): string => Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->favicon(asset('favicon.svg'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
                \App\Filament\Pages\ChangePassword::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                ForcePasswordChange::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\ValidateUserPanelRole::class,
                \App\Http\Middleware\EnsureAdminPanelAccess::class,
            ]);
            
    }
}
