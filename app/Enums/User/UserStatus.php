<?php

namespace App\Enums\User;

enum UserStatus: int
{
    case active = 1;
    case pending = 2;
    case blocked = 3;
}
