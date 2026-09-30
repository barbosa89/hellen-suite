<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\FolioChargeType;
use Database\Factories\FolioChargeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property FolioChargeType $type
 * @property string $description
 * @property int $quantity
 * @property int $unit_amount_minor
 * @property int $total_amount_minor
 * @property Carbon|null $service_start_on
 * @property Carbon|null $service_end_on
 * @property Carbon $posted_at
 * @property string $idempotency_key
 * @property int $stay_folio_id
 * @property int|null $room_occupancy_id
 * @property int|null $recorded_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['type', 'description', 'quantity', 'unit_amount_minor', 'total_amount_minor', 'service_start_on', 'service_end_on', 'posted_at', 'idempotency_key', 'room_occupancy_id', 'recorded_by_user_id'])]
class FolioCharge extends Model
{
    /** @use HasFactory<FolioChargeFactory> */
    use HasFactory;

    /** @return BelongsTo<StayFolio, $this> */
    public function folio(): BelongsTo
    {
        return $this->belongsTo(StayFolio::class, 'stay_folio_id');
    }

    /** @return BelongsTo<RoomOccupancy, $this> */
    public function roomOccupancy(): BelongsTo
    {
        return $this->belongsTo(RoomOccupancy::class);
    }

    protected function casts(): array
    {
        return [
            'type' => FolioChargeType::class,
            'service_start_on' => 'date:Y-m-d',
            'service_end_on' => 'date:Y-m-d',
            'posted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
