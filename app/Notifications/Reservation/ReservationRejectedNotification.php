<?php

namespace App\Notifications\Reservation;

use App\Enums\Notification\NotificationType;
use App\Models\ReservationRequest;
use App\Notifications\Base\BaseNotification;
use Illuminate\Notifications\Notification;

class ReservationRejectedNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(private ReservationRequest $reservationRequest)
    {
        parent::__construct();
    }

    public function getType(): int
    {
        return NotificationType::reservation_rejected->value;
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
                'en' => __('notifications.reservation_rejected', locale: 'en'),
                'ar' => __('notifications.reservation_rejected', locale: 'ar'),
            ],
            'body' => [
                'en' => __('notifications.reservation_rejected_body', locale: 'en'),
                'ar' => __('notifications.reservation_rejected_body', locale: 'ar'),
            ],
            'data' => [
                'item_id' => $this->reservationRequest->id,
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
