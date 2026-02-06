<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContractType;

class ContractTypePolicy
{
    /**
     * Apenas ADMIN pode visualizar tipos de contrato
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode visualizar um tipo de contrato específico
     */
    public function view(User $user, ContractType $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode criar tipos de contrato
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode editar tipos de contrato
     */
    public function update(User $user, ContractType $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar tipos de contrato
     */
    public function delete(User $user, ContractType $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode restaurar tipos de contrato eliminados
     */
    public function restore(User $user, ContractType $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente tipos de contrato
     */
    public function forceDelete(User $user, ContractType $model): bool
    {
        return $user->role === 'admin';
    }
}
