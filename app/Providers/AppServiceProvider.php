<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define simple ability gates based on roles.
        Gate::define('manage-users', fn (?User $user) => $user?->isAdmin() || $user?->isRoot());
        Gate::define('manage-employees', fn (?User $user) => $user?->isHr() || $user?->isAdmin() || $user?->isRoot());
        Gate::define('manage-contracts', fn (?User $user) => $user?->isHr() || $user?->isAdmin() || $user?->isRoot());
        Gate::define('manage-timeoffs', fn (?User $user) => $user?->isHr() || $user?->isAdmin() || $user?->isRoot());
        Gate::define('reset-passwords', fn (?User $user) => $user?->isAdmin() || $user?->isRoot());
        Gate::define('view-own', fn (?User $user, $modelUserId = null) => $user?->isRoot() || ($user?->isEmployee() && $user->id === $modelUserId));

        // Additional convenience gates for common management areas
        $areas = [
            'worklogs' => 'manage-worklogs',
            'designations' => 'manage-designations',
            'contract-types' => 'manage-contracttypes',
            'hoursbanks' => 'manage-hoursbanks',
            'reference' => 'manage-reference',
        ];

        foreach ($areas as $area => $ability) {
            Gate::define($ability, fn (?User $user) => $user?->isHr() || $user?->isAdmin() || $user?->isRoot());
        }

        // Register route middleware alias 'role' so routes can use ->middleware('role:admin')
        if ($this->app->bound('router')) {
            $this->app->router->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);

            // Register middleware to redirect already-authenticated users away from login pages
            $this->app->router->aliasMiddleware('redirect.login', \App\Http\Middleware\RedirectAuthenticatedFromLogin::class);
        }

        // Share helpful login links with the welcome and auth.login views so the views act as a bridge
        view()->composer(['welcome', 'auth.login'], function ($view) {
            $view->with('panelLoginRoutes', [
                'admin' => Route::has('filament.admin.auth.login') ? route('filament.admin.auth.login') : '/admin/login',
                'employee' => Route::has('filament.employee.auth.login') ? route('filament.employee.auth.login') : '/employee/login',
                'hr' => '/hr/login',
                'generic' => route('login'),
            ]);
        });
    }
}
