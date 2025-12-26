<?php

namespace App\Enums;

use App\Traits\EnumFeatures;

enum RoleEnum: string
{
    use EnumFeatures;

    case User = 'user';
    case Doctor = 'doctor';
    case Admin = 'admin';
}
