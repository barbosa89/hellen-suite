<?php

declare(strict_types=1);

namespace App\Constants;

enum HotelType: string
{
    case MAIN = 'main';
    case HEADQUARTERS = 'headquarters';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
