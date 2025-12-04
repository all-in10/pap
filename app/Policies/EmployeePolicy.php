<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function view(?User $user, Employee $employee): bool
    {
        if ($user === null) return false;
        if ($user->isRoot()) return true;
        if ($user->isAdmin() || $user->isHr()) return true;
        // Employees can view only their own profile
        return $user->isEmployee() && $employee->user_id === $user->id;
    }

    public function create(?User $user): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function update(?User $user, Employee $employee): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function delete(?User $user, Employee $employee): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }
}
