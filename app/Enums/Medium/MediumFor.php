<?php

namespace App\Enums\Medium;

use App\Enums\Base\EnumToArray;

enum MediumFor: string
{
    use EnumToArray;

    case personal_photo = 'personal-photo';
    case id_photo = 'id-photo';
    case apartment_photo = 'apartment-photo';
}
