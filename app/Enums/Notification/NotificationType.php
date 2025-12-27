<?php

namespace App\Enums\Notification;

use App\Enums\Base\EnumToArray;

enum NotificationType: string
{
    use EnumToArray;

    case user_registration = 'user_registration';
    case user_update_id_photo = 'user_update_id_photo';
    case change_user_status = 'change_user_status';
    case reservation_request = 'reservation_request';
    case reservation_approved = 'reservation_approved';
    case reservation_modified = 'reservation_modified';
    case review_apartment = 'review_apartment';
}
