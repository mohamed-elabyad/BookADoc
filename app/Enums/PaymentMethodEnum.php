<?php

namespace App\Enums;

use App\Traits\EnumFeatures;

enum PaymentMethodEnum: string
{
    use EnumFeatures;

    case Cash = 'cash';
    case Online = 'online';
}
