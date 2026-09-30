<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use Database\Factories\CashMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property CashMovementType $type
 * @property CashMovementDirection $direction
 * @property int $amount_minor
 * @property string $currency
 * @property string|null $comment
 * @property Carbon $occurred_at
 * @property string $idempotency_key
 * @property int $hotel_id
 * @property int|null $payment_id
 * @property int|null $recorded_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'direction', 'amount_minor', 'currency', 'comment', 'occurred_at', 'idempotency_key', 'payment_id', 'recorded_by_user_id'])]
class CashMovement extends Model
{
    /** @use HasFactory<CashMovementFactory> */
    use HasFactory;

    /** @return BelongsTo<Hotel, $this> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /** @return BelongsTo<Payment, $this> */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    protected function casts(): array
    {
        return [
            'type' => CashMovementType::class,
            'direction' => CashMovementDirection::class,
            'occurred_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
