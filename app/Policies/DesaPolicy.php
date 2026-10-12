<?php

namespace App\Policies;

use App\Models\Desa;
use App\Models\User;

class DesaPolicy
{
    public function viewAny(User $user): bool        { return $user->can('view_any_desa::app'); }
    public function view(User $user, Desa $desa): bool { return $user->can('view_desa::app'); }
    public function create(User $user): bool         { return $user->can('create_desa::app'); }
    public function update(User $user, Desa $desa): bool { return $user->can('update_desa::app'); }
    public function delete(User $user, Desa $desa): bool { return $user->can('delete_desa::app'); }
    public function deleteAny(User $user): bool      { return $user->can('delete_any_desa::app'); }
    public function restore(User $user, Desa $desa): bool { return false; }
    public function forceDelete(User $user, Desa $desa): bool { return false; }
}
