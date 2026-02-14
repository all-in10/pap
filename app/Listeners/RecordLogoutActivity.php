<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\User;

/**
 * Listener para rastrear logouts de usuários
 */
class RecordLogoutActivity
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        /** @var User $user */
        $user = $event->user;

        activity()
            ->causedBy($user)
            ->log('Logout realizado por ' . $user->name);
    }
}
