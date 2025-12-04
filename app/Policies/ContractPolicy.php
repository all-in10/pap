<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function view(?User $user, Contract $contract): bool
    {
        if ($user === null) return false;
        if ($user->isRoot()) return true;
        if ($user->isAdmin() || $user->isHr()) return true;
        // Employees can view only their own contracts
        if ($user->isEmployee()) {
            return $contract->employee_id === $user->employee?->id;
        }
        return false;
    }

    public function create(?User $user): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function update(?User $user, Contract $contract): bool
    {
        return $user?->isHr() || $user?->isAdmin() || $user?->isRoot();
    }

    public function delete(?User $user, Contract $contract): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }

    public function restore(?User $user, Contract $contract): bool
    {
        return $this->delete($user, $contract);
    }

    public function forceDelete(?User $user, Contract $contract): bool
    {
        return $this->delete($user, $contract);
    }
}
