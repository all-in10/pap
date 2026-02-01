<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\AuditLog;

class AuditLogPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isRoot() ?? false;
    }

    public function view(?User $user, AuditLog $log): bool
    {
        return $user?->isRoot() ?? false;
    }
}
