<?php

namespace App\Contracts;

interface Dictionary
{
    /**
     * @return array<string, string>
     */
    public static function toDictionary(): array;
}
