<?php

namespace App\Enums\Notification;

use App\Enums\Base\EnumToArray;

enum NotificationType: string
{
    use EnumToArray;

    case RESERVATION_APPROVED = 'reservation_approved';
    case RESERVATION_REJECTED = 'reservation_rejected';
    case RESERVATION_PENDING = 'reservation_pending';
    case RESERVATION_REQUEST = 'reservation_request';
    case APARTMENT_LISTED = 'apartment_listed';
    case APARTMENT_UPDATED = 'apartment_updated';
    case REVIEW_RECEIVED = 'review_received';
    case USER_APPROVED = 'user_approved';
    case USER_REJECTED = 'user_rejected';
    case USER_BLOCKED = 'user_blocked';
    case GENERAL = 'general';
}
