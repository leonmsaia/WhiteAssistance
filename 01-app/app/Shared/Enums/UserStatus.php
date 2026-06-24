<?php

namespace App\Shared\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
}
