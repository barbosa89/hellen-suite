<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\RoomOccupancyEventType;
use Database\Factories\RoomOccupancyEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property RoomOccupancyEventType $type
 * @property array<array-key, mixed>|null $before_data
 * @property array<array-key, mixed> $after_data
 * @property int $room_occupancy_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'before_data', 'after_data', 'user_id'])]
class RoomOccupancyEvent extends Model
{
    /** @use HasFactory<RoomOccupancyEventFactory> */
    use HasFactory;

    /** @return BelongsTo<RoomOccupancy, $this> */
    public function roomOccupancy(): BelongsTo
    {
        return $this->belongsTo(RoomOccupancy::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => RoomOccupancyEventType::class,
            'before_data' => 'array',
            'after_data' => 'array',
        ];
    }
}
