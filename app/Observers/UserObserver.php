<?php

namespace App\Observers;

use App\Enums\User\UserStatus;
use App\Models\User;
use App\Notifications\ChangeUserStatusNotification;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    public function updated(User $user)
    {
        if ($user->isDirty('id_photo') && !$user->isAdmin()) {
            $user->status = UserStatus::pending;
            $user->saveQuietly();
            
            Notification::send(User::admins()->get(), new ChangeUserStatusNotification($user));
        }
    }
}
