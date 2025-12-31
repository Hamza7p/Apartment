<?php

namespace App\Notifications\Reservation;

use App\Enums\Notification\NotificationType;
use App\Models\ReservationModification;
use App\Notifications\Base\BaseNotification;

class ReservationModificationRejectedNotification extends BaseNotification
{
    public function __construct(
        private ReservationModification $modification
    ) {
        parent::__construct();
    }

    public function getType(): int
    {
        return NotificationType::reservation_modification_rejected->value;
    }

    /**
     * Channels
     */
    public function via(object $notifiable): array
    {
        return ['database', 'fcm'];
    }

    /**
     * Shared payload
     */
    protected function payload(): array
    {
        return [
            'title' => [
                'en' => __('notifications.reservation_modification_rejected.title', locale: 'en'),
                'ar' => __('notifications.reservation_modification_rejected.title', locale: 'ar'),
            ],
            'body' => [
                'en' => __('notifications.reservation_modification_rejected.body', [
                    'id' => $this->modification->reservation_id,
                ], 'en'),
                'ar' => __('notifications.reservation_modification_rejected.body', [
                    'id' => $this->modification->reservation_id,
                ], 'ar'),
            ],
            'data' => [
                'reservation_id' => $this->modification->reservation_id,
                'modification_id' => $this->modification->id,
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
