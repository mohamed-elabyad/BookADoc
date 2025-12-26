<?php

namespace App\Traits;

trait EnumFeatures
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toSelect(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->name])
            ->toArray();
    }
}
