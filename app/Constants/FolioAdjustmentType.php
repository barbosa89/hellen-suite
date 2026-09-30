<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum FolioAdjustmentType: string
{
    use EnumArrayable;

    case Courtesy = 'courtesy';

    case WriteOff = 'write_off';

    case Discount = 'discount';

    case Correction = 'correction';
}
