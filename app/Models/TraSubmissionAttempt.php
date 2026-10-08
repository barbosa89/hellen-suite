<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $tra_submission_id
 * @property string $channel
 * @property string $request_hash
 * @property int|null $response_status
 * @property string|null $response_reference
 * @property string|null $error_category
 * @property string|null $error_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'tra_submission_id',
    'channel',
    'request_hash',
    'response_status',
    'response_reference',
    'error_category',
    'error_message',
])]
class TraSubmissionAttempt extends Model
{
    /** @return BelongsTo<TraSubmission, $this> */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(TraSubmission::class, 'tra_submission_id');
    }
}
