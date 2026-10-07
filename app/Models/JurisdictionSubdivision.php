<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\JurisdictionSubdivisionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $country_code
 * @property string $code
 * @property string $name
 * @property string $type
 * @property string|null $iso_reference
 * @property string|null $source
 * @property string|null $version
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['country_code', 'code', 'name', 'type', 'iso_reference', 'source', 'version', 'is_active'])]
class JurisdictionSubdivision extends Model
{
    /** @use HasFactory<JurisdictionSubdivisionFactory> */
    use HasFactory;

    /** @return HasMany<JurisdictionLocality, $this> */
    public function localities(): HasMany
    {
        return $this->hasMany(JurisdictionLocality::class, 'subdivision_id');
    }

    /**
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeForCountry(Builder $query, string $countryCode): Builder
    {
        return $query->where('country_code', $countryCode);
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
