<?php

namespace App\Policies;

use App\Models\User;
use App\Models\State;

class StatePolicy
{
    /**
     * Apenas ADMIN pode visualizar estados
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode visualizar um estado específico
     */
    public function view(User $user, State $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode criar estados
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode editar estados
     */
    public function update(User $user, State $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar estados
     */
    public function delete(User $user, State $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode restaurar estados eliminados
     */
    public function restore(User $user, State $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente estados
     */
    public function forceDelete(User $user, State $model): bool
    {
        return $user->role === 'admin';
    }
}
