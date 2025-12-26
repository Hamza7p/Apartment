<?php

namespace App\Policies;

use App\Models\System;
use App\Models\User;

class SystemPolicy
{
    public function view(User $user, ?System $system = null): bool
    {
        return $user->isAdmin();
    }
}
