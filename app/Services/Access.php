<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\UserRole;

class Access
{
    /**
     * Obtém o usuário autenticado atual ou retorna o usuário fornecido
     * @param User|null $user
     * @return User|null
     */
    private static function getUser(?User $user = null): ?User
    {
        return $user ?? Auth::user();
    }

    /**
     * Verifica se o usuário pode gerenciar registos de trabalho (HR/Admin/Root)
     * @param User|null $user
     * @return bool
     */
    public static function canManageWorklogs(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && Gate::allows('manage-worklogs', $user);
    }

    /**
     * Verifica se o usuário tem papel de funcionário (não pode gerenciar registos)
     * @param User|null $user
     * @return bool
     */
    public static function isEmployeeRole(?User $user = null): bool
    {
        $user = self::getUser($user);
        if ($user === null) {
            return false;
        }
        return $user->isEmployee();
    }

    /**
     * Verifica se o usuário tem um papel específico
     * Para 'employee', requer igualdade exata; para outros, verifica privilégio hierárquico
     * @param string|UserRole $role
     * @param User|null $user
     * @return bool
     */
    public static function hasRole(string|UserRole $role, ?User $user = null): bool
    {
        $user = self::getUser($user);
        if ($user === null) {
            return false;
        }

        // Converte string para enum se necessário
        if (is_string($role)) {
            $role = UserRole::tryFrom($role) ?? UserRole::EMPLOYEE;
        }

        // Para 'employee', requer igualdade exata (employee != hr/admin/root)
        if ($role === UserRole::EMPLOYEE) {
            return $user->isEmployee();
        }

        // Para outros papéis, verifica privilégio hierárquico (ex.: admin >= hr)
        return $user->hasPrivilegeOf($role);
    }

    /**
     * Verifica se o usuário está pelo menos no nível HR
     * @param User|null $user
     * @return bool
     */
    public static function isHr(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isHr();
    }

    /**
     * Verifica se o usuário é admin ou root
     * @param User|null $user
     * @return bool
     */
    public static function isAdmin(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isAdmin();
    }

    /**
     * Verifica se o usuário é root
     * @param User|null $user
     * @return bool
     */
    public static function isRoot(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isRoot();
    }
}

