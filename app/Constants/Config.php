<?php

namespace App\Constants;

use App\Contracts\Dictionary;
use App\Contracts\Translatable;
use App\Contracts\StaticArrayable;
use App\Traits\CanTranslate;

class Config implements StaticArrayable, Dictionary, Translatable
{
    use CanTranslate;

    public const CHECK_OUT= 'out';

    public static function toArray(): array
    {
        return [
            self::CHECK_OUT,
        ];
    }

    public static function toDictionary(): array
    {
        return [
            self::CHECK_OUT => trans('configurations.out'),
        ];
    }
}
