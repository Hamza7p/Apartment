<?php

namespace App\Notifications\Channels;

use App\Models\Notification as NotificationModel;
use Illuminate\Notifications\Notification;

class DatabaseChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toDatabase')) {
            return;
        }

        $payload = $notification->toDatabase($notifiable);
        
        NotificationModel::create([
            'id'      => $notification->id,
            'user_id' => $notifiable->id,
            'type'    => $notification->getType(),
            'title'   => $payload['title'] ?? [],
            'body'    => $payload['body'] ?? [],
            'data'    => $payload['data'] ?? [],
        ]);
    }
}
