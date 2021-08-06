<?php

namespace App\Constants;

use App\Contracts\Dictionary;
use App\Contracts\Translatable;
use App\Contracts\StaticArrayable;

class Config implements StaticArrayable, Dictionary, Translatable
{
    public const CHECK_OUT= 'out';

    public static function toArray(): array
    {
        return [
            self::CHECK_OUT,
        ];
    }

    public static function toDict(): array
    {
        return [
            self::CHECK_OUT => trans('configurations.out'),
        ];
    }

    public static function trans(string $key): string
    {
        return self::toDict()[$key] ?? '';
    }
}
