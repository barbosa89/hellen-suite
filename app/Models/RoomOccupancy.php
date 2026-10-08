<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\RoomOccupancyEndReason;
use Database\Factories\RoomOccupancyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $stay_id
 * @property int|null $stay_folio_id
 * @property int $room_id
 * @property int|null $principal_guest_id
 * @property string $nightly_rate
 * @property Carbon $checked_in_at
 * @property Carbon $expected_check_out_on
 * @property Carbon|null $checked_out_at
 * @property RoomOccupancyEndReason|null $end_reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'stay_id',
    'stay_folio_id',
    'room_id',
    'principal_guest_id',
    'nightly_rate',
    'checked_in_at',
    'expected_check_out_on',
    'checked_out_at',
    'end_reason',
])]
class RoomOccupancy extends Model
{
    /** @use HasFactory<RoomOccupancyFactory> */
    use HasFactory;

    /** @return BelongsTo<Stay, $this> */
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    /** @return BelongsTo<StayFolio, $this> */
    public function folio(): BelongsTo
    {
        return $this->belongsTo(StayFolio::class, 'stay_folio_id');
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function principalGuest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'principal_guest_id');
    }

    /** @return BelongsToMany<Guest, $this> */
    public function guests(): BelongsToMany
    {
        return $this->belongsToMany(Guest::class)->withTimestamps();
    }

    /** @return HasMany<RoomOccupancyEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(RoomOccupancyEvent::class);
    }

    /** @return HasMany<FolioCharge, $this> */
    public function charges(): HasMany
    {
        return $this->hasMany(FolioCharge::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'nightly_rate' => 'decimal:2',
            'checked_in_at' => 'datetime:Y-m-d H:i:s',
            'expected_check_out_on' => 'date:Y-m-d',
            'checked_out_at' => 'datetime:Y-m-d H:i:s',
            'end_reason' => RoomOccupancyEndReason::class,
        ];
    }
}
