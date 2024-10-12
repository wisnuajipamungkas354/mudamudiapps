<?php

namespace App\Policies;

use App\Models\Daerah;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DaerahPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Daerah $daerah): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Daerah $daerah): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Daerah $daerah): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Daerah $daerah): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Daerah $daerah): bool
    {
        if (!$user->hasRole('Admin')) {
            return false;
        } else {
            return true;
        }
    }
}
