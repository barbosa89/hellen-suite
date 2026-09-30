<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum CashMovementType: string
{
    use EnumArrayable;

    case StayPayment = 'stay_payment';

    case PaymentRefund = 'payment_refund';

    case ManualEntry = 'manual_entry';

    case Withdrawal = 'withdrawal';
}
