<?php

namespace App\Enums\Apartment;

enum ApartmentStatus: string
{
    // available, under_maintenance
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';

}
