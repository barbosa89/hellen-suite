<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum HousekeepingStatus: string
{
    use EnumArrayable;

    case Clean = 'clean';

    case Dirty = 'dirty';
}
