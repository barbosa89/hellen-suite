<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\HousekeepingStatus;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $room_type_id
 * @property string $number
 * @property string|null $floor
 * @property string $reference_price
 * @property HousekeepingStatus $housekeeping_status
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'room_type_id',
    'number',
    'floor',
    'reference_price',
    'housekeeping_status',
    'is_active',
])]
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'housekeeping_status' => HousekeepingStatus::Clean->value,
        'is_active' => true,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'reference_price' => 'decimal:2',
            'housekeeping_status' => HousekeepingStatus::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Hotel, $this>
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * @return BelongsTo<RoomType, $this>
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /** @return HasMany<RoomOccupancy, $this> */
    public function roomOccupancies(): HasMany
    {
        return $this->hasMany(RoomOccupancy::class);
    }
}
