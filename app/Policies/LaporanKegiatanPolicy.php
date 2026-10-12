<?php

namespace App\Policies;

use App\Models\LaporanKegiatan;
use App\Models\User;

class LaporanKegiatanPolicy
{
    public function viewAny(User $user): bool { return $user->can('view_any_laporan::kegiatan'); }
    public function view(User $user, LaporanKegiatan $laporanKegiatan): bool { return $user->can('view_laporan::kegiatan'); }
    public function create(User $user): bool { return $user->can('create_laporan::kegiatan'); }
    public function update(User $user, LaporanKegiatan $laporanKegiatan): bool { return $user->can('update_laporan::kegiatan'); }
    public function delete(User $user, LaporanKegiatan $laporanKegiatan): bool { return $user->can('delete_laporan::kegiatan'); }
    public function deleteAny(User $user): bool { return $user->can('delete_any_laporan::kegiatan'); }
}
