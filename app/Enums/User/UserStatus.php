<?php

namespace App\Enums\User;

use App\Enums\Base\EnumToArray;

enum UserStatus: int
{
    use EnumToArray;

    case approved = 1;
    case pending = 2;
    case rejected = 3;
    case blocked = 4;
}
