<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum FolioAdjustmentDirection: string
{
    use EnumArrayable;

    case Debit = 'debit';

    case Credit = 'credit';
}
