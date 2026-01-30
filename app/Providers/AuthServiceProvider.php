<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Contract;
use App\Models\Timeoff;
use App\Models\Employee;
use App\Models\User;
use App\Policies\ContractPolicy;
use App\Policies\TimeoffPolicy;
use App\Models\Worklog;
use App\Policies\WorklogPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Contract::class => ContractPolicy::class,
        Timeoff::class => TimeoffPolicy::class,
        Employee::class => EmployeePolicy::class,
        Worklog::class => WorklogPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        // Ensure users are redirected to their portal after login when no other intended URL is set
        // Listens to the Login event and sets the `url.intended` session key accordingly.
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            $user = $event->user;

            // Respect an existing intended URL (user may have been trying to access a specific page)
            $intended = session()->get('url.intended');
            $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;

            // Only override intended if there's no intended path or it's pointing to the generic / or /app
            if ($path && $path !== '/' && strpos($path, '/app') !== 0) {
                return;
            }

            if ($user->isEmployee()) {
                session()->put('url.intended', url('/employee'));
                return;
            }

            if ($user->isAdmin() || $user->isRoot()) {
                session()->put('url.intended', url('/admin'));
                return;
            }

            if ($user->isHr()) {
                session()->put('url.intended', url('/hr'));
                return;
            }

            // Fallback to the generic app panel
            session()->put('url.intended', url('/app'));
        });

        // Define gates for role-based access
        Gate::define('manage-worklogs', function (User $user) {
            return $user->isHr() || $user->isAdmin() || $user->isRoot();
        });

        Gate::define('manage-employees', function (User $user) {
            return $user->isHr() || $user->isAdmin() || $user->isRoot();
        });

        Gate::define('manage-contracts', function (User $user) {
            return $user->isHr() || $user->isAdmin() || $user->isRoot();
        });

        Gate::define('manage-timeoffs', function (User $user) {
            return $user->isHr() || $user->isAdmin() || $user->isRoot();
        });

        Gate::define('manage-system', function (User $user) {
            return $user->isAdmin() || $user->isRoot();
        });

        Gate::define('is-admin', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('is-root', function (User $user) {
            return $user->isRoot();
        });
    }
}
