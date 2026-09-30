<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\FolioAdjustmentType;
use Database\Factories\FolioAdjustmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property FolioAdjustmentType $type
 * @property FolioAdjustmentDirection $direction
 * @property int $amount_minor
 * @property string $reason
 * @property Carbon $occurred_at
 * @property string $idempotency_key
 * @property int $stay_folio_id
 * @property int|null $folio_charge_id
 * @property int|null $recorded_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'direction', 'amount_minor', 'reason', 'occurred_at', 'idempotency_key', 'folio_charge_id', 'recorded_by_user_id'])]
class FolioAdjustment extends Model
{
    /** @use HasFactory<FolioAdjustmentFactory> */
    use HasFactory;

    /** @return BelongsTo<StayFolio, $this> */
    public function folio(): BelongsTo
    {
        return $this->belongsTo(StayFolio::class, 'stay_folio_id');
    }

    protected function casts(): array
    {
        return [
            'type' => FolioAdjustmentType::class,
            'direction' => FolioAdjustmentDirection::class,
            'occurred_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
