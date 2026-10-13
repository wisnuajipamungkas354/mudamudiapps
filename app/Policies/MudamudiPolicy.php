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
        if (! $user->can('update_mudamudiapp')) {
            return false;
        }

        $role = $user->getAppRoleName();

        return match ($role) {
            'MM Daerah'   => $mudamudi->daerah?->nm_daerah === $user->detail,
            'MM Desa'     => $mudamudi->desa?->nm_desa === $user->detail,
            'MM Kelompok' => $mudamudi->kelompok?->nm_kelompok === $user->detail,
            default       => false,
        };
    }

    public function delete(User $user, Mudamudi $mudamudi): bool
    {
        if (! $user->can('delete_mudamudiapp')) {
            return false;
        }

        $role = $user->getAppRoleName();

        return match ($role) {
            'MM Daerah'   => $mudamudi->daerah?->nm_daerah === $user->detail,
            'MM Desa'     => $mudamudi->desa?->nm_desa === $user->detail,
            'MM Kelompok' => $mudamudi->kelompok?->nm_kelompok === $user->detail,
            default       => false,
        };
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
