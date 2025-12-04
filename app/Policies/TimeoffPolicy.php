<?php

namespace App\Policies;

use App\Models\Timeoff;
use App\Models\User;

class TimeoffPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot() || $user?->isEmployee();
    }

    public function view(?User $user, Timeoff $timeoff): bool
    {
        if ($user === null) return false;
        if ($user->isRoot()) return true;
        if ($user->isAdmin() || $user->isHr()) return true;
        if ($user->isEmployee()) {
            return $timeoff->employee_id === $user->employee?->id;
        }
        return false;
    }

    public function create(?User $user): bool
    {
        // HR and employees can create timeoffs (employees create their own requests)
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot() || $user?->isEmployee();
    }

    public function update(?User $user, Timeoff $timeoff): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function delete(?User $user, Timeoff $timeoff): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }
}
