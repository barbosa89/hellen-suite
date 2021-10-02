<?php

namespace App\Constants;

use App\Traits\CanTranslate;
use App\Contracts\Dictionary;
use App\Contracts\Translatable;
use App\Contracts\StaticArrayable;

class Genders implements StaticArrayable, Dictionary, Translatable
{
    use CanTranslate;

    public const MALE = 'm';
    public const FEMALE = 'f';

    public static function toArray(): array
    {
        return [
            self::MALE,
            self::FEMALE,
        ];
    }

    public static function toDictionary(): array
    {
        return [
            self::MALE => trans('common.m'),
            self::FEMALE  => trans('common.f'),
        ];
    }
}
