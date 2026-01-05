<?php

namespace App\Notifications;

use App\Enums\Notification\NotificationType;
use App\Notifications\Base\BaseNotification;

class SendOtpNotification extends BaseNotification
{
    public function __construct(
        public string $phone,
        public string $otp
    ) {
        parent::__construct();
    }

    public function getType(): int
    {
        return NotificationType::otp->value;
    }

    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    /**
     * SMS payload
     */
    public function toSms(object $notifiable): array
    {
        return [
            'phone'   => $this->phone,
            'message' => "Your OTP is {$this->otp}",
        ];
    }
}
