<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\ReservationEventType;
use Database\Factories\ReservationEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
