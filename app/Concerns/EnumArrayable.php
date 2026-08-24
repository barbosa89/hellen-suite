<?php

declare(strict_types=1);

namespace App\Concerns;

trait EnumArrayable
{
    /** @return array<int, string> */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
