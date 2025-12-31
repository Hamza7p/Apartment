<?php

namespace App\Notifications\Reservation;

use App\Enums\Notification\NotificationType;
use App\Models\Reservation;
use App\Notifications\Base\BaseNotification;
use Illuminate\Notifications\Notification;

class ReservationAcceptedNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private Reservation $reservation)
    {
        parent::__construct();
    }

    public function getType(): int
    {
        return NotificationType::reservation_accepted->value;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'fcm'];
    }

    public function payload()
    {
        return [
            'title' => [
                'en' => __('notifications.reservation_accepted', locale: 'en'),
                'ar' => __('notifications.reservation_accepted', locale: 'ar'),
            ],
            'body' => [
                'en' => __('notifications.reservation_accepted_body', locale: 'en'),
                'ar' => __('notifications.reservation_accepted_body', locale: 'ar'),
            ],
            'data' => [
                'item_id' => $this->reservation->id,
            ],

        ];
    }

    /**
     * Database channel
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * FCM channel
     */
    public function toFcm(object $notifiable): array
    {
        return $this->payload();
    }
}
