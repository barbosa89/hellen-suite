<?php

declare(strict_types=1);

namespace App\Models;

use App\Constants\IdentificationTypeCode;
use Database\Factories\IdentificationTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property IdentificationTypeCode $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @mixin \Eloquent
 */
#[Fillable(['code'])]
class IdentificationType extends Model
{
    /** @use HasFactory<IdentificationTypeFactory> */
    use HasFactory;

    /** @return HasMany<Guest, $this> */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'code' => IdentificationTypeCode::class,
        ];
    }
}
