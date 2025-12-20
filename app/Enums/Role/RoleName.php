<?php

namespace App\Enums\Role;

use App\Enums\Base\EnumToArray;

enum RoleName: string
{
    use EnumToArray;
    
    case admin = 'admin';
    case user = 'user';
}
