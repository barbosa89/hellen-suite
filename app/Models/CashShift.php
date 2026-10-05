<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CashShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $hotel_id
 * @property int $number
 * @property int|null $previous_cash_shift_id
 * @property Carbon $opened_at
 * @property Carbon|null $closed_at
 * @property string|null $opening_note
 * @property string|null $closing_note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['number', 'previous_cash_shift_id', 'opened_at', 'closed_at', 'opening_note', 'closing_note'])]
class CashShift extends Model
{
    /** @use HasFactory<CashShiftFactory> */
    use HasFactory;

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function previousShift(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_cash_shift_id');
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(CashShiftReconciliation::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime:Y-m-d H:i:s',
            'closed_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
