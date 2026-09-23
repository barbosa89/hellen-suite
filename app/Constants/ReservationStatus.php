<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum ReservationStatus: string
{
    use EnumArrayable;

    case Draft = 'draft';

    case Confirmed = 'confirmed';

    case CheckedIn = 'checked_in';

    case Cancelled = 'cancelled';

    case NoShow = 'no_show';
}
