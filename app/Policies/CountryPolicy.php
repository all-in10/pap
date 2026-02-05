<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Country;

class CountryPolicy
{
    /**
     * Apenas ADMIN pode visualizar países
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode visualizar um país específico
     */
    public function view(User $user, Country $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode criar países
     */
    public function create(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode editar países
     */
    public function update(User $user, Country $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode eliminar países
     */
    public function delete(User $user, Country $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode restaurar países eliminados
     */
    public function restore(User $user, Country $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente países
     */
    public function forceDelete(User $user, Country $model): bool
    {
        return $user->role === 'ADMIN';
    }
}
