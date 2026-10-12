<?php

namespace App\Policies;

use App\Models\Kelompok;
use App\Models\User;

class KelompokPolicy
{
    public function viewAny(User $user): bool        { return $user->can('view_any_kelompok::app'); }
    public function view(User $user, Kelompok $kelompok): bool { return $user->can('view_kelompok::app'); }
    public function create(User $user): bool         { return $user->can('create_kelompok::app'); }
    public function update(User $user, Kelompok $kelompok): bool { return $user->can('update_kelompok::app'); }
    public function delete(User $user, Kelompok $kelompok): bool { return $user->can('delete_kelompok::app'); }
    public function deleteAny(User $user): bool      { return $user->can('delete_any_kelompok::app'); }
    public function restore(User $user, Kelompok $kelompok): bool { return false; }
    public function forceDelete(User $user, Kelompok $kelompok): bool { return false; }
}
