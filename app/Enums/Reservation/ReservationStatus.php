<?php

namespace App\Enums\Reservation;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case ENDED = 'ended';
    case CANCELED = 'canceled';
    case CANCEL_REQUEST = 'cancel_request';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case EXPIRED = 'expired';
}

