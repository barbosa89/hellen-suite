<?php

namespace App\Contracts;

interface Translatable
{
    public static function trans(string $key): string;
}
