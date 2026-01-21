<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worklog;

class WorklogPolicy
{
    /**
     * Determina se o usuário pode visualizar qualquer registo de trabalho
     * HR/Admin/Root podem ver todos; funcionários podem ver os próprios via view()
     * @param ?User $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // HR/Admin/Root can view all; employees can view their own via view()
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot() || $user?->isEmployee();
    }

    /**
     * Determina se o usuário pode visualizar um registo de trabalho específico
     * Root vê tudo; HR/Admin vêem tudo; Funcionários só os próprios
     * @param ?User $user
     * @param Worklog $worklog
     * @return bool
     */
    public function view(?User $user, Worklog $worklog): bool
    {
        if ($user === null) return false;
        if ($user->isRoot()) return true;
        if ($user->isAdmin() || $user->isHr()) return true;
        // Employee can view only their own worklogs
        if ($user->isEmployee()) {
            return $worklog->employee_id === $user->employee?->id;
        }
        return false;
    }

    /**
     * Determina se o usuário pode criar registos de trabalho
     * Funcionários e HR/Admin podem criar (funcionários para si mesmos)
     * @param ?User $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        // Employees and HR/Admin can create worklogs (employees for themselves)
        return $user?->isEmployee() || $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    /**
     * Determina se o usuário pode atualizar um registo de trabalho
     * Apenas HR/Admin/Root podem editar
     * @param ?User $user
     * @param Worklog $worklog
     * @return bool
     */
    public function update(?User $user, Worklog $worklog): bool
    {
        // HR/Admin/Root can edit worklogs
        return $user?->isRoot() || $user?->isAdmin() || $user?->isHr();
    }

    /**
     * Determina se o usuário pode excluir um registo de trabalho
     * Apenas Admin/Root podem excluir
     * @param ?User $user
     * @param Worklog $worklog
     * @return bool
     */
    public function delete(?User $user, Worklog $worklog): bool
    {
        // Only Admin/Root can delete worklogs
        return $user?->isRoot() || $user?->isAdmin();
    }
}
