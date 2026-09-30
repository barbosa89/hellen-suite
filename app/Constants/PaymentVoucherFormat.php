<?php

declare(strict_types=1);

namespace App\Constants;

enum PaymentVoucherFormat: string
{
    case A4 = 'a4';

    case Thermal = 'thermal';
}
