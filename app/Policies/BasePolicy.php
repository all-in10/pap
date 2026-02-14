<?php

namespace App\Policies;

use App\Models\User;

/**
 * BasePolicy fornece métodos comuns para policies
 */
class BasePolicy
{
    /**
     * Verifica se o usuário é admin
     */
    protected function isAdmin(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Verifica se o usuário é admin ou HR
     */
    protected function isAdminOrHR(User $user): bool
    {
        return in_array($user->role, ['admin', 'hr']);
    }

    /**
     * Verifica se é o mesmo usuário
     */
    protected function isOwnUser(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    /**
     * Método padrão para exportação (admin/HR)
     */
    public function export(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    /**
     * Método padrão para visualizar auditoria (apenas admin)
     */
    public function viewAudit(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
