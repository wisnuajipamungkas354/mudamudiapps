<?php

namespace App\Policies;

use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PengurusPolicy
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
    public function view(User $user, Pengurus $pengurus): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pengurus $pengurus): bool
    {
        $role = auth()->user()->roles;
        
        if ($user->can('edit pengurus') && $role[0]->name == $pengurus->role && $pengurus->nm_tingkatan == $user->detail) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pengurus $pengurus): bool
    {
        $role = auth()->user()->roles;
        
        if ($user->can('edit pengurus') && $role[0]->name == $pengurus->role && $pengurus->nm_tingkatan == $user->detail) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function deleteAny(User $user): bool
    {
        $role = auth()->user()->roles;
        
        if ($user->can('edit pengurus')) {
            return true;
        } else {
            return false;
        }
    }
}
