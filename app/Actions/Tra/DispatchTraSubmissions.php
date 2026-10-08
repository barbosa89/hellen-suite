<?php

declare(strict_types=1);

namespace App\Actions\Tra;

use App\Constants\TraSubmissionStatus;
use App\Jobs\SendTraSubmission;
use App\Models\Stay;

final class DispatchTraSubmissions
{
    public function execute(Stay $stay): void
    {
        $stay->traSubmissions()
            ->where('status', TraSubmissionStatus::Pending)
            ->pluck('id')
            ->each(fn (int $id): mixed => SendTraSubmission::dispatch($id));
    }
}
