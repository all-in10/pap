<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait EnforceEmployeeRole
{
    /**
     * Verifica se o usuário autenticado tem role 'employee'
     * 
     * @return bool
     */
    protected function isAuthenticatedAsEmployee(): bool
    {
        $user = Auth::user();
        return $user && strtolower((string) $user->role) === 'employee';
    }
}
