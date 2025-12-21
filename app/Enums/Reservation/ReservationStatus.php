<?php

namespace App\Enums\Reversation;

enum ReservationStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case ENDED = 'ended';
    case CANCELED = 'canceled';
}
