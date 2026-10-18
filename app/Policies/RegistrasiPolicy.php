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
    public function delete(User $user, Registrasi $registrasi): bool { return $user->can('delete_registrasi') || $user->can('reject_registrasi'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_registrasi') || $user->can('reject_registrasi'); }
    public function share(User $user): bool { return $user->can('share_registrasi'); }
    public function apply(User $user): bool { return $user->can('apply_registrasi'); }
    public function reject(User $user, Registrasi $registrasi): bool { return $user->can('reject_registrasi') || $user->can('delete_registrasi'); }
    public function rejectAny(User $user): bool { return $user->can('reject_registrasi') || $user->can('delete_any_registrasi'); }
}
