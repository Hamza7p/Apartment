<?php

namespace App\Enums\Notification;

use App\Enums\Base\EnumToArray;

enum NotificationType: int
{
    use EnumToArray;

    case general_notification = 1;
    case users = 2;
    case user_status = 3;
    case apartments = 4;
    case reviews = 5;
    case reservations = 6;
    case reservation_requests = 7;
    case reservation_approvals = 8;
}

