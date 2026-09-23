<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum ReservationEventType: string
{
    use EnumArrayable;

    case Created = 'created';

    case Updated = 'updated';

    case Confirmed = 'confirmed';

    case Cancelled = 'cancelled';

    case NoShow = 'no_show';

    case CheckedIn = 'checked_in';
}
