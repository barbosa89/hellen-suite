<?php

namespace App\Constants;

use App\Traits\CanTranslate;
use App\Contracts\Dictionary;
use App\Contracts\Translatable;
use App\Contracts\StaticArrayable;

class Modules implements StaticArrayable, Dictionary, Translatable
{
    use CanTranslate;

    public const ALL = 'all';
    public const USERS = 'users';
    public const HOTELS = 'hotels';

    public static function toArray(): array
    {
        return [
            self::ALL,
            self::USERS,
            self::HOTELS,
        ];
    }

    public static function toDictionary(): array
    {
        return [
            self::ALL => trans('common.all'),
            self::USERS => trans('users.title'),
            self::HOTELS  => trans('hotels.title'),
        ];
    }
}
