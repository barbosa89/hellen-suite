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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['guest_id', 'role'])]
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
        return ['role' => StayGuestRole::class];
    }
}
