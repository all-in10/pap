<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Timeoff;

class TimeoffPolicy
{
    /**
     * Admin tem acesso total
     */
    public function before(User $user): ?bool
    {
        if ($user->role === 'ADMIN') {
            return true;
        }

        return null;
    }

    /**
     * EMPLOYEE, ADMIN e HR podem visualizar lista de férias
     */
    public function viewAny(User $user): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR', 'EMPLOYEE']);
    }

    /**
     * EMPLOYEE pode visualizar apenas suas próprias férias
     * ADMIN e HR podem visualizar qualquer uma
     */
    public function view(User $user, Timeoff $timeoff): bool
    {
        $role = strtoupper($user->role);
        
        if ($role === 'HR') {
            return true;
        }

        // Employee apenas vê suas próprias férias
        if ($role === 'EMPLOYEE') {
            return $timeoff->employee_id === $user->employee_id;
        }

        return false;
    }

    /**
     * EMPLOYEE, ADMIN e HR podem criar férias
     */
    public function create(User $user): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR', 'EMPLOYEE']);
    }

    /**
     * Apenas ADMIN e HR podem editar
     */
    public function update(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR']);
    }

    /**
     * Apenas ADMIN e HR podem eliminar
     */
    public function delete(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR']);
    }

    /**
     * Apenas ADMIN e HR podem restaurar
     */
    public function restore(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR']);
    }

    /**
     * Apenas ADMIN e HR podem eliminar permanentemente
     */
    public function forceDelete(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtoupper($user->role), ['ADMIN', 'HR']);
    }
}
