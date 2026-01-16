<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Worklog;

class WorklogPolicy
{
    public function viewAny(?User $user): bool
    {
        // HR/Admin/Root can view all; employees can view their own via view()
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot() || $user?->isEmployee();
    }

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

    public function create(?User $user): bool
    {
        // Employees and HR/Admin can create worklogs (employees for themselves)
        return $user?->isEmployee() || $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function update(?User $user, Worklog $worklog): bool
    {
        // HR/Admin/Root can edit worklogs
        return $user?->isRoot() || $user?->isAdmin() || $user?->isHr();
    }

    public function delete(?User $user, Worklog $worklog): bool
    {
        // Only Admin/Root can delete worklogs
        return $user?->isRoot() || $user?->isAdmin();
    }
}
