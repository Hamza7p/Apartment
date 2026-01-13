<?php

namespace App\Notifications;

use App\Enums\Notification\NotificationType;
use App\Models\User;
use App\Notifications\Base\BaseNotification;

class RegistrationNotification extends BaseNotification
{
    public function __construct(
        private User $user
    ) {
        parent::__construct();
    }

    public function getType(): int
    {
        return NotificationType::users->value;
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
                'en' => __('messages.register.title', locale: 'en'),
                'ar' => __('messages.register.title', locale: 'ar'),
            ],
            'body' => [
                'en' => __('messages.register.body', ['id' => $this->user->id, 'name' => $this->user->first_name], 'en'),
                'ar' => __('messages.register.body', ['id' => $this->user->id, 'name' => $this->user->first_name], 'ar'),
            ],
            'data' => [
                'item_id' => $this->user->id,
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
