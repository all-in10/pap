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
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * EMPLOYEE, ADMIN e HR podem visualizar lista de férias
     */
    public function viewAny(User $user): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr', 'employee']);
    }

    /**
     * EMPLOYEE pode visualizar apenas suas próprias férias
     * ADMIN e HR podem visualizar qualquer uma
     */
    public function view(User $user, Timeoff $timeoff): bool
    {
        $role = strtolower($user->role);
        
        if ($role === 'hr') {
            return true;
        }

        // Employee apenas vê suas próprias férias
        if ($role === 'employee') {
            return $timeoff->employee_id === $user->employee_id;
        }

        return false;
    }

    /**
     * EMPLOYEE, ADMIN e HR podem criar férias
     */
    public function create(User $user): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr', 'employee']);
    }

    /**
     * Apenas ADMIN e HR podem editar
     */
    public function update(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr']);
    }

    /**
     * Apenas ADMIN e HR podem eliminar
     */
    public function delete(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr']);
    }

    /**
     * Apenas ADMIN e HR podem restaurar
     */
    public function restore(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr']);
    }

    /**
     * Apenas ADMIN e HR podem eliminar permanentemente
     */
    public function forceDelete(User $user, Timeoff $timeoff): bool
    {
        return in_array(strtolower($user->role), ['admin', 'hr']);
    }
}
