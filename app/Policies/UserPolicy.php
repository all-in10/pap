<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(?User $user): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }

    public function view(?User $user, User $model): bool
    {
        // Only Admin/Root can view other users
        return $user?->isAdmin() || $user?->isRoot();
    }

    public function create(?User $user): bool
    {
        return $user?->isAdmin() || $user?->isRoot();
    }

    public function update(?User $user, User $model): bool
    {
        // Admin/Root can update users
        return $user?->isAdmin() || $user?->isRoot();
    }

    public function delete(?User $user, User $model): bool
    {
        // Only Root can permanently delete users; Admin will use soft-delete action
        return $user?->isRoot();
    }

    public function restore(?User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }

    public function forceDelete(?User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
