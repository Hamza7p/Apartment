<?php

namespace App\Enums\Medium;

use App\Enums\Base\EnumToArray;

enum MediumType: int
{
    use EnumToArray;
    
    case image = 1;
    case video = 2;
    case audio = 3;
    case document = 4;
    case other = 5;
}
