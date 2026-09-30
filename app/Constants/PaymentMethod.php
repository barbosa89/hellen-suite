<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum PaymentMethod: string
{
    use EnumArrayable;

    case Cash = 'cash';

    case BankTransfer = 'bank_transfer';
}
