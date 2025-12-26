<?php

namespace App\Enums;

use App\Traits\EnumFeatures;

enum StatusEnum: string
{
    use EnumFeatures;

    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
}
