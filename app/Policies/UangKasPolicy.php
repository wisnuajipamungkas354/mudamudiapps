<?php

namespace App\Policies;

use App\Models\UangKas;
use App\Models\User;

class UangKasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_uang::kas');
    }

    public function view(User $user, UangKas $uangKas): bool
    {
        return $user->can('view_uang::kas');
    }

    public function create(User $user): bool
    {
        return $user->can('create_uang::kas');
    }

    public function update(User $user, UangKas $uangKas): bool
    {
        return $user->can('update_uang::kas');
    }

    public function delete(User $user, UangKas $uangKas): bool
    {
        return $user->can('delete_uang::kas');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_uang::kas');
    }

    public function restore(User $user, UangKas $uangKas): bool
    {
        return false;
    }

    public function forceDelete(User $user, UangKas $uangKas): bool
    {
        return false;
    }
}
