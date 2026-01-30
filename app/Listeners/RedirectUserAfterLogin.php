<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class RedirectUserAfterLogin
{
    /**
     * Marca que o utilizador acaba de fazer login
     * O middleware HandlePostLoginRedirect vai redirecionar apropriadamente
     */
    public function handle(Login $event): void
    {
        // Marcar na sessão que o utilizador acaba de fazer login
        session()->put('just_logged_in', true);
    }
}
