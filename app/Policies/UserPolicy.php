<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends BasePolicy
{
    /**
     * Apenas ADMIN pode visualizar utilizadores
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode visualizar um utilizador específico
     */
    public function view(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode criar utilizadores
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode editar utilizadores
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode eliminar utilizadores
     */
    public function delete(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode restaurar utilizadores eliminados
     */
    public function restore(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Apenas ADMIN pode eliminar permanentemente
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Admin e HR podem exportar utilizadores
     */
    public function export(User $user): bool
    {
        return parent::export($user);
    }

    /**
     * Apenas ADMIN pode visualizar auditoria de utilizadores
     */
    public function viewAudit(User $user): bool
    {
        return parent::viewAudit($user);
    }
}

