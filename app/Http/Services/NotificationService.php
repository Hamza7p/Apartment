<?php 

namespace App\Http\Services;

class NotificationService
{
    public function getUserNotifications($user, $unreadOnly = false)
    {
        $query = $user->notifications();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->get();
    }
}