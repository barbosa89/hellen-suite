<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\HotelObserver;
use Database\Factories\HotelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $business_name
 * @property string $tin
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $email
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[ObservedBy([HotelObserver::class])]
#[Fillable(['business_name', 'tin', 'address', 'phone', 'mobile', 'email', 'image'])]
class Hotel extends Model
{
    /** @use HasFactory<HotelFactory> */
    use HasFactory;

    /**
     * @return HasMany<Room, $this>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * @return HasMany<RoomType, $this>
     */
    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    /** @return HasMany<Guest, $this> */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /** @return HasMany<Stay, $this> */
    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function (null|string $value): null|string {
                if ($value === null || str_starts_with($value, 'http')) {
                    return $value;
                }

                return route('hotels.image', $this);
            },
        );
    }
}
