<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;

class StatusPolicy
{
    public function viewAny(User $user): bool { return false; }
    public function view(User $user, Status $status): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, Status $status): bool { return false; }
    public function delete(User $user, Status $status): bool { return false; }
    public function deleteAny(User $user): bool { return false; }
    public function restore(User $user, Status $status): bool { return false; }
    public function forceDelete(User $user, Status $status): bool { return false; }
}
