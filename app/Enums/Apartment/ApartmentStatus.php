<?php

namespace App\Enums\Apartment;

enum ApartmentStatus: string
{
    // available, reserved, under_maintenance
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case UNDER_MAINTENANCE = 'under_maintenance';

}
