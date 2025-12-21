<?php

namespace App\Observers;

use App\Enums\User\UserStatus;
use App\Models\User;

class UserObserver
{
    public function updated(User $user)
    {
        if ($user->isDirty('id_photo')) {
            $user->status = UserStatus::pending;
            $user->saveQuietly();
        }
    }
}
