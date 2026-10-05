<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\PaymentMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cash_shift_id
 * @property PaymentMethod $payment_method
 * @property string $currency
 * @property int $opening_minor
 * @property int $inflow_minor
 * @property int $outflow_minor
 * @property int|null $expected_closing_minor
 * @property int|null $declared_closing_minor
 * @property int|null $difference_minor
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['payment_method', 'currency', 'opening_minor', 'inflow_minor', 'outflow_minor', 'expected_closing_minor', 'declared_closing_minor', 'difference_minor'])]
class CashShiftReconciliation extends Model
{
    public function cashShift(): BelongsTo
    {
        return $this->belongsTo(CashShift::class);
    }

    protected function casts(): array
    {
        return ['payment_method' => PaymentMethod::class];
    }
}
