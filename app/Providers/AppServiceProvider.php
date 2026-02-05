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

        // Aplicar middleware que força a alteração de senha após autenticação
        if ($this->app->runningInConsole() === false) {
            $router = $this->app->make(\Illuminate\Routing\Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\EnforcePasswordChange::class);
        }
    }
}
