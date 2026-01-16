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
