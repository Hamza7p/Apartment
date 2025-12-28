<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;

class FcmChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toFcm')) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        // هنا الإرسال الحقيقي إلى Firebase
        // مثال (Pseudo Code):
        // Firebase::send(
        //     token: $notifiable->fcm_token,
        //     title: $payload['title'],
        //     body: $payload['body'],
        //     data: $payload['data'],
        // );
    }
}
