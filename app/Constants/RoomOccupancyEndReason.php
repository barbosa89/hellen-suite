<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum RoomOccupancyEndReason: string
{
    use EnumArrayable;

    case CheckOut = 'check_out';

    case Transfer = 'transfer';
}
