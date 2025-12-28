<?php

namespace App\Notifications\Base;

use App\Enums\Notification\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {
        $this->id ??= (string) Str::uuid();
    }

    public function getType(): int
    {
        return NotificationType::general_notification->value;
    }
}