<?php

namespace App\Policies;

use App\Models\Daerah;
use App\Models\User;

class DaerahPolicy
{
    public function viewAny(User $user): bool { return false; }
    public function view(User $user, Daerah $daerah): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, Daerah $daerah): bool { return false; }
    public function delete(User $user, Daerah $daerah): bool { return false; }
    public function deleteAny(User $user): bool { return false; }
    public function restore(User $user, Daerah $daerah): bool { return false; }
    public function forceDelete(User $user, Daerah $daerah): bool { return false; }
}
