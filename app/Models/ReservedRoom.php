<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ReservedRoomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['room_id', 'nightly_rate', 'planned_check_in_on', 'planned_check_out_on'])]
class ReservedRoom extends Model
{
    /** @use HasFactory<ReservedRoomFactory> */
    use HasFactory;

    /** @return BelongsTo<Reservation, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsToMany<ReservationGuest, $this> */
    public function reservationGuests(): BelongsToMany
    {
        return $this->belongsToMany(ReservationGuest::class)->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'nightly_rate' => 'decimal:2',
            'planned_check_in_on' => 'date:Y-m-d',
            'planned_check_out_on' => 'date:Y-m-d',
        ];
    }
}
