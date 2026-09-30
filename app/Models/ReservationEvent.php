<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\ReservationEventType;
use Database\Factories\ReservationEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ReservationEventType $type
 * @property array<array-key, mixed>|null $before_data
 * @property array<array-key, mixed> $after_data
 * @property int $reservation_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'before_data', 'after_data'])]
class ReservationEvent extends Model
{
    /** @use HasFactory<ReservationEventFactory> */
    use HasFactory;

    /** @return BelongsTo<Reservation, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'type' => ReservationEventType::class,
            'before_data' => 'array',
            'after_data' => 'array',
        ];
    }
}
