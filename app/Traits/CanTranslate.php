<?php

namespace App\Traits;

trait CanTranslate {
    public static function trans(string $key): string
    {
        return self::toDictionary()[$key] ?? '';
    }
}
