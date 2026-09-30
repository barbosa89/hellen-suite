<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StayFolioFactory;
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
 * @property int|null $closed_by_user_id
 * @property string $label
 * @property string $currency
 * @property Carbon|null $closed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['hotel_id', 'stay_id', 'label', 'currency', 'closed_at', 'closed_by_user_id'])]
class StayFolio extends Model
{
    /** @use HasFactory<StayFolioFactory> */
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

    /** @return HasMany<RoomOccupancy, $this> */
    public function roomOccupancies(): HasMany
    {
        return $this->hasMany(RoomOccupancy::class);
    }

    /** @return HasMany<FolioCharge, $this> */
    public function charges(): HasMany
    {
        return $this->hasMany(FolioCharge::class);
    }

    /** @return HasMany<FolioAdjustment, $this> */
    public function adjustments(): HasMany
    {
        return $this->hasMany(FolioAdjustment::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['closed_at' => 'datetime:Y-m-d H:i:s'];
    }
}
