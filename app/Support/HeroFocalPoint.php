<?php

namespace App\Support;

final class HeroFocalPoint
{
    public const DEFAULT = 'center center';

    public static function options(): array
    {
        return [
            'left top', 'center top', 'right top',
            'left center', self::DEFAULT, 'right center',
            'left bottom', 'center bottom', 'right bottom',
        ];
    }

    public static function normalise(?string $value): string
    {
        return in_array($value, self::options(), true) ? $value : self::DEFAULT;
    }
}
