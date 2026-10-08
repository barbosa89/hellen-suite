<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use Database\Factories\TraSubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $stay_id
 * @property int $room_occupancy_id
 * @property int $stay_guest_id
 * @property TraSubmissionKind $kind
 * @property TraSubmissionStatus $status
 * @property string $idempotency_key
 * @property array<string, mixed>|null $payload_snapshot
 * @property string|null $external_reference
 * @property string|null $last_error_code
 * @property string|null $last_error_message
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'hotel_id',
    'stay_id',
    'room_occupancy_id',
    'stay_guest_id',
    'kind',
    'status',
    'idempotency_key',
    'payload_snapshot',
    'external_reference',
    'last_error_code',
    'last_error_message',
    'sent_at',
])]
class TraSubmission extends Model
{
    /** @use HasFactory<TraSubmissionFactory> */
    use HasFactory;

    /** @return BelongsTo<Hotel, $this> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<Stay, $this> */
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    /** @return BelongsTo<RoomOccupancy, $this> */
    public function roomOccupancy(): BelongsTo
    {
        return $this->belongsTo(RoomOccupancy::class);
    }

    /** @return BelongsTo<StayGuest, $this> */
    public function stayGuest(): BelongsTo
    {
        return $this->belongsTo(StayGuest::class);
    }

    /** @return HasMany<TraSubmissionAttempt, $this> */
    public function attempts(): HasMany
    {
        return $this->hasMany(TraSubmissionAttempt::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'kind' => TraSubmissionKind::class,
            'status' => TraSubmissionStatus::class,
            'payload_snapshot' => 'array',
            'sent_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
