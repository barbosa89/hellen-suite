<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum FolioChargeType: string
{
    use EnumArrayable;

    case Lodging = 'lodging';

    case Manual = 'manual';
}
