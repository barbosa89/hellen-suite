<?php

namespace App\Constants;

use App\Traits\CanTranslate;
use App\Contracts\Dictionary;
use App\Contracts\Translatable;
use App\Contracts\StaticArrayable;

class IdentificationTypes implements StaticArrayable, Dictionary, Translatable
{
    use CanTranslate;

    public const CC = 'cc';
    public const CI = 'ci';
    public const EC = 'ec';
    public const IT = 'it';
    public const RC = 'rc';
    public const TP = 'tp';
    public const DNI = 'dni';
    public const DUI = 'dui';

    public static function toArray(): array
    {
        return [
            self::CC,
            self::CI,
            self::EC,
            self::IT,
            self::RC,
            self::TP,
            self::DNI,
            self::DUI,
        ];
    }

    public static function toDictionary(): array
    {
        return [
            self::CC => trans('common.cc'),
            self::CI => trans('common.ci'),
            self::EC => trans('common.ec'),
            self::IT => trans('common.it'),
            self::RC => trans('common.rc'),
            self::TP => trans('common.tp'),
            self::DNI => trans('common.dni'),
            self::DUI => trans('common.dui'),
        ];
    }
}
