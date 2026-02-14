<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\User;

/**
 * Listener para rastrear logins de usuários
 */
class RecordLoginActivity
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        activity()
            ->causedBy($user)
            ->log('Login realizado por ' . $user->name);
    }
}
