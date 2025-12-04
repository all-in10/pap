<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(?User $user): bool
    {
        // Allow HR/Admin/Root to view all employees, and allow an employee to access the list
        // so they can view their own profile (filtration is applied at Resource level).
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot() || $user?->isEmployee();
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
        // Allow HR/Admin/Root to update any employee. Employees may update their own profile.
        if ($user === null) return false;
        if ($user->isRoot() || $user->isAdmin() || $user->isHr()) return true;
        if ($user->isEmployee()) {
            return $employee->user_id === $user->id;
        }
        return false;
    }

    public function delete(?User $user, Employee $employee): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }
}
