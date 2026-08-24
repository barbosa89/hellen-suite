<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RoomTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property string $name
 * @property int $capacity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'name',
    'capacity',
])]
class RoomType extends Model
{
    /** @use HasFactory<RoomTypeFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Hotel, $this>
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}
