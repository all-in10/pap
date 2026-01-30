<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\TimeoffApproved;
use App\Events\TimeoffRejected;
use App\Events\ContractExpiringReminder;
use App\Listeners\CreateTimeoffApprovedNotification;
use App\Listeners\CreateTimeoffRejectedNotification;
use App\Listeners\SendContractExpiringReminder;
use App\Listeners\RedirectUserAfterLogin;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            RedirectUserAfterLogin::class,
        ],
        TimeoffApproved::class => [
            CreateTimeoffApprovedNotification::class,
        ],
        TimeoffRejected::class => [
            CreateTimeoffRejectedNotification::class,
        ],
        ContractExpiringReminder::class => [
            SendContractExpiringReminder::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
