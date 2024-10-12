<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mudamudi;
use Illuminate\Auth\Access\Response;

class MudamudiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mudamudi $mudamudi): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can('tambah mm')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mudamudi $mudamudi): bool
    {
        if ($user->can('edit mm') && $mudamudi->kelompok->nm_kelompok == $user->detail) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mudamudi $mudamudi): bool
    {
        if ($user->can('hapus mm') && $mudamudi->kelompok->nm_kelompok == $user->detail) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAny(User $user): bool
    {
        if ($user->can('hapus mm')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mudamudi $mudamudi): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Mudamudi $mudamudi): bool
    {
        return true;
    }
}
