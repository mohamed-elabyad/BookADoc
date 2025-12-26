<?php

namespace App\Enums;

use App\Traits\EnumFeatures;

enum PaymentStatusEnum: string
{
    use EnumFeatures;

    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
