<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Kelompok;

class KelompokPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Kelompok $kelompok): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kelompok $kelompok): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kelompok $kelompok): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Kelompok $kelompok): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Kelompok $kelompok): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('MM Daerah')) {
            return true;
        } else {
            return false;
        }
    }
}
