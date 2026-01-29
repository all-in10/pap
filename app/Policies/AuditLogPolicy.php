<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuditLog;

class AuditLogPolicy
{
    /**
     * Determine whether the user can view audit logs.
     */
    public function viewAny(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->isRoot() || $user->isAdmin() || $user->isHr();
    }

    /**
     * Determine whether the user can view a specific audit log.
     */
    public function view(?User $user, AuditLog $log): bool
    {
        return $this->viewAny($user);
    }
}
