<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Timeoff;
use App\Enums\RoleEnum;

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
     * Mas não podem editar seus próprios pedidos (auto-aprovação bloqueada)
     */
    public function update(User $user, Timeoff $timeoff): bool
    {
        // First check if user is admin or hr
        if (!in_array(strtolower($user->role), ['admin', 'hr'])) {
            return false;
        }

        // Prevent self-approval: check if user created this timeoff request
        if ($this->isOwnRequest($user, $timeoff)) {
            return false;
        }

        return true;
    }

    /**
     * Método específico para aprovar pedidos
     * Apenas ADMIN e HR podem aprovar, mas não podem aprovar seus próprios pedidos
     */
    public function approve(User $user, Timeoff $timeoff): bool
    {
        // Check if user is an approver (admin or hr)
        if (!RoleEnum::isApprover($user->role)) {
            return false;
        }

        // Prevent self-approval
        if ($this->isOwnRequest($user, $timeoff)) {
            return false;
        }

        // Pedido deve estar pendente para ser aprovado
        if ($timeoff->status !== 'pending') {
            return false;
        }

        return true;
    }

    /**
     * Verifica se um pedido de timeoff é do próprio usuário
     * 
     * @param User $user
     * @param Timeoff $timeoff
     * @return bool
     */
    private function isOwnRequest(User $user, Timeoff $timeoff): bool
    {
        // Get the employee associated with the timeoff
        $employeeUserId = $timeoff->employee?->user_id;
        
        // Check if the user requesting the action is the same as who created the timeoff
        return $employeeUserId === $user->id;
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
