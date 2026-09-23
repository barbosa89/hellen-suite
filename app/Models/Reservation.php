<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $responsible_guest_id
 * @property ReservationStatus $status
 * @property Carbon|null $planned_check_in_on
 * @property Carbon|null $planned_check_out_on
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $no_show_at
 * @property Carbon|null $checked_in_at
 * @property Carbon|null $checked_out_at
 */
#[Fillable([
    'responsible_guest_id',
    'status',
    'planned_check_in_on',
    'planned_check_out_on',
    'confirmed_at',
    'cancelled_at',
    'no_show_at',
    'checked_in_at',
    'checked_out_at',
])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = ['status' => ReservationStatus::Draft->value];

    /** @return array<string, string> */
    protected function casts(): array
    {
        $datetimeFormat = 'datetime:Y-m-d H:i:s';

        return [
            'status' => ReservationStatus::class,
            'planned_check_in_on' => 'date:Y-m-d',
            'planned_check_out_on' => 'date:Y-m-d',
            'confirmed_at' => $datetimeFormat,
            'cancelled_at' => $datetimeFormat,
            'no_show_at' => $datetimeFormat,
            'checked_in_at' => $datetimeFormat,
            'checked_out_at' => $datetimeFormat,
        ];
    }

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

    /** @return HasMany<ReservationGuest, $this> */
    public function reservationGuests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    /** @return HasMany<ReservedRoom, $this> */
    public function reservedRooms(): HasMany
    {
        return $this->hasMany(ReservedRoom::class);
    }

    /** @return HasMany<ReservationEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(ReservationEvent::class);
    }

    /** @return HasOne<Stay, $this> */
    public function stay(): HasOne
    {
        return $this->hasOne(Stay::class);
    }
}
