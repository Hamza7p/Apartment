<?php

namespace App\Enums\User;

enum UserStatus: int
{
    case approved = 1;
    case pending = 2;
    case rejected = 3;
    case blocked = 4;
}
