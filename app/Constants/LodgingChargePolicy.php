<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum LodgingChargePolicy: string
{
    use EnumArrayable;

    case ConsumedNights = 'consumed_nights';
}
