<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum CashMovementDirection: string
{
    use EnumArrayable;

    case In = 'in';

    case Out = 'out';
}
