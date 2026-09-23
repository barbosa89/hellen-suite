<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $identification_type_id
 * @property string $first_name
 * @property string $last_name
 * @property string $identification_number
 * @property string|null $mobile
 * @property string|null $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'identification_type_id',
    'first_name',
    'last_name',
    'identification_number',
    'mobile',
    'email',
])]
class Guest extends Model
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory;

    /** @return BelongsTo<Hotel, $this> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<IdentificationType, $this> */
    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(IdentificationType::class);
    }

    /** @return HasMany<StayGuest, $this> */
    public function stayGuests(): HasMany
    {
        return $this->hasMany(StayGuest::class);
    }

    /** @return HasMany<Stay, $this> */
    public function responsibleStays(): HasMany
    {
        return $this->hasMany(Stay::class, 'responsible_guest_id');
    }

    /** @return BelongsToMany<RoomOccupancy, $this> */
    public function roomOccupancies(): BelongsToMany
    {
        return $this->belongsToMany(RoomOccupancy::class)->withTimestamps();
    }

    /** @return HasMany<ReservationGuest, $this> */
    public function reservationGuests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    /** @return HasMany<Reservation, $this> */
    public function responsibleReservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'responsible_guest_id');
    }
}
