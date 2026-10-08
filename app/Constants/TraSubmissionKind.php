<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum TraSubmissionKind: string
{
    use EnumArrayable;

    case Principal = 'principal';

    case Companion = 'companion';
}
