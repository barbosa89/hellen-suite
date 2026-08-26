<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum StayStatus: string
{
    use EnumArrayable;

    case Active = 'active';

    case CheckedOut = 'checked_out';
}
