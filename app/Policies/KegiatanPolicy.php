<?php

namespace App\Policies;

use App\Models\Kegiatan;
use App\Models\User;

class KegiatanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kegiatan');
    }

    public function view(User $user, Kegiatan $kegiatan): bool
    {
        return $user->can('view_kegiatan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_kegiatan');
    }

    public function update(User $user, Kegiatan $kegiatan): bool
    {
        return $user->can('update_kegiatan');
    }

    public function presensi(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Kegiatan $kegiatan): bool
    {
        return $user->can('delete_kegiatan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kegiatan');
    }

    public function restore(User $user, Kegiatan $kegiatan): bool
    {
        return false;
    }

    public function forceDelete(User $user, Kegiatan $kegiatan): bool
    {
        return false;
    }
}
