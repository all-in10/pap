<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacation;
use App\Enums\RoleEnum;

class VacationPolicy extends BasePolicy
{
    public function before(User $user): ?bool
    {
        // Admin tem acesso total a TUDO
        if ($user->role === RoleEnum::ADMIN) {
            return true;
        }
        return null;  // Deixa continuar checando outros métodos
    }

    public function viewAny(User $user): bool
    {
        // Admin (já retornou true em before), HR e Employee podem ver
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR, RoleEnum::EMPLOYEE]);
    }

    public function view(User $user, Vacation $vacation): bool
    {
        // HR pode ver todas
        if ($user->role === RoleEnum::HR) {
            return true;
        }

        // Employee só vê suas próprias
        if ($user->role === RoleEnum::EMPLOYEE) {
            return $vacation->employee_id === $user->employee_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR, RoleEnum::EMPLOYEE]);
    }

    public function update(User $user, Vacation $vacation): bool
    {
        // Apenas ADMIN e HR podem editar
        if (!in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR])) {
            return false;
        }

        // HR não pode editar se é seu próprio pedido
        if ($user->role === RoleEnum::HR && $vacation->employee?->user_id === $user->id) {
            return false;
        }

        return true;
    }

    public function delete(User $user, Vacation $vacation): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR]);
    }

    public function restore(User $user, Vacation $vacation): bool
    {
        return in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR]);
    }

    public function forceDelete(User $user, Vacation $vacation): bool
    {
        return $user->role === RoleEnum::ADMIN;
    }

    /**
     * Método específico para aprovar férias
     */
    public function approve(User $user, Vacation $vacation): bool
    {
        // Apenas ADMIN e HR podem aprovar
        if (!in_array($user->role, [RoleEnum::ADMIN, RoleEnum::HR])) {
            return false;
        }

        // Não pode aprovar seu próprio pedido
        if ($vacation->employee?->user_id === $user->id) {
            return false;
        }

        // Apenas pedidos pendentes podem ser aprovados
        return $vacation->isPending();
    }
}
