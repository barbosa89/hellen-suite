<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum PaymentType: string
{
    use EnumArrayable;

    case Receipt = 'receipt';

    case Refund = 'refund';
}
