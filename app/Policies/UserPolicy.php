<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Apenas ADMIN pode visualizar utilizadores
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode visualizar um utilizador específico
     */
    public function view(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode criar utilizadores
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode editar utilizadores
     */
    public function update(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar utilizadores
     */
    public function delete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode restaurar utilizadores eliminados
     */
    public function restore(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente utilizadores
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->role === 'admin';
    }
}
