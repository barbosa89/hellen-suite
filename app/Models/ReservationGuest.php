<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\StayGuestRole;
use Database\Factories\ReservationGuestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $reservation_id
 * @property int $guest_id
 * @property StayGuestRole $role
 * @property string|null $residence_country
 * @property string|null $residence_subdivision
 * @property string|null $residence_locality
 * @property string|null $origin_country
 * @property string|null $origin_subdivision
 * @property string|null $origin_locality
 * @property string|null $destination_country
 * @property string|null $destination_subdivision
 * @property string|null $destination_locality
 * @property string|null $travel_purpose
 * @property string|null $transport_means
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'guest_id',
    'role',
    'residence_country',
    'residence_subdivision',
    'residence_locality',
    'origin_country',
    'origin_subdivision',
    'origin_locality',
    'destination_country',
    'destination_subdivision',
    'destination_locality',
    'travel_purpose',
    'transport_means',
])]
class ReservationGuest extends Model
{
    /** @use HasFactory<ReservationGuestFactory> */
    use HasFactory;

    /** @return BelongsTo<Reservation, $this> */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** @return BelongsToMany<ReservedRoom, $this> */
    public function reservedRooms(): BelongsToMany
    {
        return $this->belongsToMany(ReservedRoom::class)->withTimestamps();
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['role' => StayGuestRole::class];
    }
}
