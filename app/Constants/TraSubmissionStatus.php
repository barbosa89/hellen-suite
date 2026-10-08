<?php

declare(strict_types=1);

namespace App\Constants;

use App\Concerns\EnumArrayable;

enum TraSubmissionStatus: string
{
    use EnumArrayable;

    case Incomplete = 'incomplete';

    case Pending = 'pending';

    case Blocked = 'blocked';

    case Queued = 'queued';

    case Sending = 'sending';

    case Sent = 'sent';

    case Rejected = 'rejected';

    case Failed = 'failed';

    case Manual = 'manual';
}
