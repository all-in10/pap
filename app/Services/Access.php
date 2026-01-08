<?php

namespace App\Services;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Enums\UserRole;

class Access
{
    /**
     * Get the current authenticated user or return provided user
     * 
     * @param User|null $user
     * @return User|null
     */
    private static function getUser(?User $user = null): ?User
    {
        return $user ?? Auth::user();
    }

    /**
     * Convenience wrapper: returns true if the current user can manage worklogs (HR/Admin/Root).
     */
    public static function canManageWorklogs(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && Gate::allows('manage-worklogs', $user);
    }

    /**
     * Convenience wrapper: returns true when the given (or current) user is effectively an "employee"
     * (i.e., not allowed to manage worklogs).
     * 
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
     * Check if user has a specific role
     */
    public static function hasRole(string|UserRole $role, ?User $user = null): bool
    {
        $user = self::getUser($user);
        if ($user === null) {
            return false;
        }

        // Convert string to enum if needed
        if (is_string($role)) {
            $role = UserRole::tryFrom($role) ?? UserRole::EMPLOYEE;
        }

        // For 'employee' we require exact equality (employee != hr/admin/root).
        if ($role === UserRole::EMPLOYEE) {
            return $user->isEmployee();
        }

        // For other roles, check hierarchical privilege (e.g., admin >= hr)
        return $user->hasPrivilegeOf($role);
    }

    /**
     * Check if user is at least HR level
     */
    public static function isHr(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isHr();
    }

    /**
     * Check if user is admin or root
     */
    public static function isAdmin(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isAdmin();
    }

    /**
     * Check if user is root
     */
    public static function isRoot(?User $user = null): bool
    {
        $user = self::getUser($user);
        return $user !== null && $user->isRoot();
    }
}

