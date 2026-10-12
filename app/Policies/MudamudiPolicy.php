<?php

namespace App\Policies;

use App\Models\Mudamudi;
use App\Models\User;

class MudamudiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_mudamudiapp');
    }

    public function view(User $user, Mudamudi $mudamudi): bool
    {
        return $user->can('view_mudamudiapp');
    }

    public function create(User $user): bool
    {
        return $user->can('create_mudamudiapp');
    }

    public function update(User $user, Mudamudi $mudamudi): bool
    {
        return $user->can('update_mudamudiapp')
            && $mudamudi->kelompok?->nm_kelompok === $user->detail;
    }

    public function delete(User $user, Mudamudi $mudamudi): bool
    {
        return $user->can('delete_mudamudiapp')
            && $mudamudi->kelompok?->nm_kelompok === $user->detail;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_mudamudiapp');
    }

    public function restore(User $user, Mudamudi $mudamudi): bool
    {
        return false;
    }

    public function forceDelete(User $user, Mudamudi $mudamudi): bool
    {
        return false;
    }
}
