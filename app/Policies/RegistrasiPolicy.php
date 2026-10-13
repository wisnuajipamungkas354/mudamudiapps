<?php

namespace App\Policies;

use App\Models\Registrasi;
use App\Models\User;

class RegistrasiPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_registrasi'); }
    public function view(User $user, Registrasi $registrasi): bool { return $user->can('view_registrasi'); }
    public function create(User $user): bool { return $user->can('create_registrasi'); }
    public function update(User $user, Registrasi $registrasi): bool { return $user->can('update_registrasi'); }
    public function delete(User $user, Registrasi $registrasi): bool { return $user->can('delete_registrasi'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_registrasi'); }
    public function share(User $user): bool { return $user->can('share_registrasi'); }
}
