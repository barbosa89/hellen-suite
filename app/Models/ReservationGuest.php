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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['guest_id', 'role'])]
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
