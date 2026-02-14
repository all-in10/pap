<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\RecordLoginActivity;
use App\Listeners\RecordLogoutActivity;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            RecordLoginActivity::class,
        ],
        Logout::class => [
            RecordLogoutActivity::class,
        ],
    ];

    /**
     * Discover automatically.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
