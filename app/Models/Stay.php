<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\StayStatus;
use Database\Factories\StayFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $responsible_guest_id
 * @property StayStatus $status
 * @property Carbon $checked_in_at
 * @property Carbon $expected_check_out_on
 * @property Carbon|null $checked_out_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'responsible_guest_id',
    'status',
    'checked_in_at',
    'expected_check_out_on',
    'checked_out_at',
])]
class Stay extends Model
{
    /** @use HasFactory<StayFactory> */
    use HasFactory;

    /** @return BelongsTo<Hotel, $this> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function responsibleGuest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'responsible_guest_id');
    }

    /** @return HasMany<StayGuest, $this> */
    public function stayGuests(): HasMany
    {
        return $this->hasMany(StayGuest::class);
    }

    /** @return HasMany<RoomOccupancy, $this> */
    public function roomOccupancies(): HasMany
    {
        return $this->hasMany(RoomOccupancy::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status' => StayStatus::class,
            'checked_in_at' => 'datetime:Y-m-d H:i:s',
            'expected_check_out_on' => 'date:Y-m-d',
            'checked_out_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
