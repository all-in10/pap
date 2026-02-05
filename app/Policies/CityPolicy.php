<?php

namespace App\Policies;

use App\Models\User;
use App\Models\City;

class CityPolicy
{
    /**
     * Apenas ADMIN pode visualizar cidades
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode visualizar uma cidade específica
     */
    public function view(User $user, City $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode criar cidades
     */
    public function create(User $user): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode editar cidades
     */
    public function update(User $user, City $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode eliminar cidades
     */
    public function delete(User $user, City $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode restaurar cidades eliminadas
     */
    public function restore(User $user, City $model): bool
    {
        return $user->role === 'ADMIN';
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente cidades
     */
    public function forceDelete(User $user, City $model): bool
    {
        return $user->role === 'ADMIN';
    }
}
