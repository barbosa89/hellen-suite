<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum RoomOccupancyEventType: string
{
    use EnumArrayable;

    case CheckedOut = 'checked_out';
}
