<?php

namespace App\Policies;

use App\Models\Pengurus;
use App\Models\User;

class PengurusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pengurus::app');
    }

    public function view(User $user, Pengurus $pengurus): bool
    {
        return $user->can('view_pengurus::app');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pengurus::app');
    }

    public function update(User $user, Pengurus $pengurus): bool
    {
        return $user->can('update_pengurus::app') && $this->ownsRecord($user, $pengurus);
    }

    public function delete(User $user, Pengurus $pengurus): bool
    {
        return $user->can('delete_pengurus::app') && $this->ownsRecord($user, $pengurus);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pengurus::app');
    }

    /** Pengurus milik tingkatan user sendiri (role + nama tingkatan sama). */
    private function ownsRecord(User $user, Pengurus $pengurus): bool
    {
        return $pengurus->role === $user->getAppRoleName()
            && $pengurus->nm_tingkatan === $user->detail;
    }
}
