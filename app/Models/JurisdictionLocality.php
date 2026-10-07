<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\JurisdictionLocalityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $subdivision_id
 * @property string $code
 * @property string $name
 * @property string|null $type
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['subdivision_id', 'code', 'name', 'type', 'is_active'])]
class JurisdictionLocality extends Model
{
    /** @use HasFactory<JurisdictionLocalityFactory> */
    use HasFactory;

    /** @return BelongsTo<JurisdictionSubdivision, $this> */
    public function subdivision(): BelongsTo
    {
        return $this->belongsTo(JurisdictionSubdivision::class, 'subdivision_id');
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
