<?php

namespace App\Constants;

use App\Contracts\StaticArrayable;

class Config implements StaticArrayable
{
    public const CHECK_OUT= 'out';

    public static function toArray(): array
    {
        return [
            self::CHECK_OUT,
        ];
    }
}
