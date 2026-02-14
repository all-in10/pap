<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\ContractType;
use App\Models\Timeoff;
use App\Policies\UserPolicy;
use App\Policies\CountryPolicy;
use App\Policies\StatePolicy;
use App\Policies\CityPolicy;
use App\Policies\ContractTypePolicy;
use App\Policies\TimeoffPolicy;

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
        // Registar Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Country::class, CountryPolicy::class);
        Gate::policy(State::class, StatePolicy::class);
        Gate::policy(City::class, CityPolicy::class);
        Gate::policy(ContractType::class, ContractTypePolicy::class);
        Gate::policy(Timeoff::class, TimeoffPolicy::class);

        // Gates para permissões gerais
        Gate::define('export-data', fn (User $user) => $user->role !== 'employee');
        Gate::define('view-audit', fn (User $user) => $user->role === 'admin');
        Gate::define('is-admin', fn (User $user) => $user->role === 'admin');
        Gate::define('is-admin-or-hr', fn (User $user) => in_array($user->role, ['admin', 'hr']));

        // Aplicar middleware que força a alteração de senha após autenticação
        if ($this->app->runningInConsole() === false) {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\EnforcePasswordChange::class);
        }
    }
}
