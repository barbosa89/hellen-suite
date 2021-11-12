<?php

namespace App\Constants;

use App\Contracts\StaticArrayable;

class Env implements StaticArrayable
{
    public const LOCAL = 'local';
    public const PRODUCTION= 'production';

    public static function toArray(): array
    {
        return [
            self::LOCAL,
            self::PRODUCTION,
        ];
    }
}
