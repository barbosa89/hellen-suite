<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum ComplianceScheme: string
{
    use EnumArrayable;

    case Tra = 'tra';

    case Generic = 'generic';

    public static function forJurisdiction(null|string $jurisdiction): self
    {
        return $jurisdiction === 'CO' ? self::Tra : self::Generic;
    }
}
