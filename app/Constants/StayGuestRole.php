<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum StayGuestRole: string
{
    use EnumArrayable;

    case Responsible = 'responsible';

    case Companion = 'companion';
}
