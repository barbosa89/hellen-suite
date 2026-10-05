<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\PaymentMethod;
use App\Constants\PaymentType;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property PaymentType $type
 * @property PaymentMethod $method
 * @property int $amount_minor
 * @property string $currency
 * @property string|null $comment
 * @property string|null $support_path
 * @property Carbon $paid_at
 * @property string $idempotency_key
 * @property int $stay_folio_id
 * @property int|null $parent_payment_id
 * @property int|null $recorded_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'method', 'amount_minor', 'currency', 'comment', 'support_path', 'paid_at', 'idempotency_key', 'parent_payment_id', 'cash_shift_id', 'recorded_by_user_id'])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    /** @return BelongsTo<StayFolio, $this> */
    public function folio(): BelongsTo
    {
        return $this->belongsTo(StayFolio::class, 'stay_folio_id');
    }

    /** @return BelongsTo<Payment, $this> */
    public function parentPayment(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_payment_id');
    }

    /** @return HasMany<Payment, $this> */
    public function refunds(): HasMany
    {
        return $this->hasMany(self::class, 'parent_payment_id');
    }

    /** @return BelongsTo<User, $this> */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    /** @return HasOne<PaymentVoucher, $this> */
    public function voucher(): HasOne
    {
        return $this->hasOne(PaymentVoucher::class);
    }

    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class);
    }

    protected function supportPath(): Attribute
    {
        return Attribute::make(
            get: fn (null|string $value): null|string => $value === null
                ? null
                : route('hotels.stays.payments.support', [$this->folio->hotel_id, $this->folio->stay_id, $this]),
        );
    }

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'method' => PaymentMethod::class,
            'paid_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
