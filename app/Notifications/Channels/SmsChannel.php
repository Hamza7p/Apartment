<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        /**
         * It must return an array in the following format:
         * [
         *   'phone'   => string,
         *   'message' => string,
         * ]
         */
        $payload = $notification->toSms($notifiable);

        if (
            ! is_array($payload) ||
            empty($payload['phone']) ||
            empty($payload['message'])
        ) {
            return;
        }

        // ==================================================
        //  SMS Service
        // ==================================================

        try {
            // Example:
            // SmsService::send(
            //     $payload['phone'],
            //     $payload['message']
            // );

            Log::info('SMS sent', [
                'phone'   => $payload['phone'],
                'message' => $payload['message'],
            ]);
        } catch (\Throwable $e) {
            Log::error('SMS sending failed', [
                'phone'   => $payload['phone'],
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
