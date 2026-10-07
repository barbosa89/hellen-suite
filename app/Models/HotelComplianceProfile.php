<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\ComplianceScheme;
use Database\Factories\HotelComplianceProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

use function is_array;

/**
 * @property int $id
 * @property int $hotel_id
 * @property string $jurisdiction
 * @property ComplianceScheme $scheme
 * @property string|null $establishment_code
 * @property array<string, mixed>|null $credentials
 * @property bool $enabled
 * @property Carbon|null $verified_at
 * @property bool $configured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $exists
 *
 * @mixin \Eloquent
 */
#[Fillable(['jurisdiction', 'scheme', 'establishment_code', 'credentials', 'enabled', 'verified_at'])]
class HotelComplianceProfile extends Model
{
    /** @use HasFactory<HotelComplianceProfileFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $hidden = ['credentials'];

    /** @var list<string> */
    protected $appends = ['configured'];

    public static function schemeForJurisdiction(null|string $jurisdiction): ComplianceScheme
    {
        return ComplianceScheme::forJurisdiction($jurisdiction);
    }

    /** @return BelongsTo<Hotel, $this> */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function isConfigured(): bool
    {
        $credentials = $this->credentials;

        return is_array($credentials) && filled($credentials['secret'] ?? null);
    }

    protected function configured(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->isConfigured(),
        );
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'scheme' => ComplianceScheme::class,
            'credentials' => 'encrypted:array',
            'enabled' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }
}
