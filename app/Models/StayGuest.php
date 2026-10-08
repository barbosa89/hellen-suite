<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\StayGuestRole;
use Database\Factories\StayGuestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $stay_id
 * @property int $guest_id
 * @property StayGuestRole $role
 * @property Carbon|null $checked_in_at
 * @property Carbon|null $checked_out_at
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
    'checked_in_at',
    'checked_out_at',
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
class StayGuest extends Model
{
    /** @use HasFactory<StayGuestFactory> */
    use HasFactory;

    /** @return BelongsTo<Stay, $this> */
    public function stay(): BelongsTo
    {
        return $this->belongsTo(Stay::class);
    }

    /** @return BelongsTo<Guest, $this> */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'role' => StayGuestRole::class,
            'checked_in_at' => 'datetime:Y-m-d H:i:s',
            'checked_out_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
