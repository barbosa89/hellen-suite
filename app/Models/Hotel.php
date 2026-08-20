<?php

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
use Illuminate\Support\Facades\Storage;

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

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => $value === null || str_starts_with($value, 'http')
                ? $value
                : Storage::disk('public')->url($value),
        );
    }
}
