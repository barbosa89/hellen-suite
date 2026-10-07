<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\ComplianceScheme;
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
 * @property string|null $country_code
 * @property string|null $timezone
 * @property HotelComplianceProfile|null $current_compliance_profile
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[ObservedBy([HotelObserver::class])]
#[Fillable(['business_name', 'tin', 'address', 'phone', 'mobile', 'email', 'image', 'country_code', 'timezone'])]
class Hotel extends Model
{
    /** @use HasFactory<HotelFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $appends = ['current_compliance_profile'];

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

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<StayFolio, $this> */
    public function stayFolios(): HasMany
    {
        return $this->hasMany(StayFolio::class);
    }

    /** @return HasMany<CashMovement, $this> */
    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    /** @return HasMany<CashShift, $this> */
    public function cashShifts(): HasMany
    {
        return $this->hasMany(CashShift::class);
    }

    /** @return HasMany<PaymentVoucher, $this> */
    public function paymentVouchers(): HasMany
    {
        return $this->hasMany(PaymentVoucher::class);
    }

    /** @return HasMany<HotelComplianceProfile, $this> */
    public function complianceProfiles(): HasMany
    {
        return $this->hasMany(HotelComplianceProfile::class);
    }

    public function complianceStrategy(): string
    {
        if ($this->country_code === 'CO') {
            return 'co-tra';
        }

        return ComplianceScheme::Generic->value;
    }

    public function resolveCurrentComplianceProfile(): null|HotelComplianceProfile
    {
        if (blank($this->country_code)) {
            return null;
        }

        return $this->complianceProfiles()
            ->where('jurisdiction', $this->country_code)
            ->where('scheme', HotelComplianceProfile::schemeForJurisdiction($this->country_code))
            ->first();
    }

    protected function currentComplianceProfile(): Attribute
    {
        return Attribute::make(
            get: fn (): null|HotelComplianceProfile => $this->resolveCurrentComplianceProfile(),
        );
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
