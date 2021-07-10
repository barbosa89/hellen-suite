<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Company extends Model
{
    use LogsActivity;
    use Queryable;

    public const SCOPE_FILTERS = [
        'from_date',
    ];

    protected $appends = ['hash'];

    protected $hidden = ['id'];

    public function vouchers(): HasMany
    {
        return $this->hasMany(\App\Models\Voucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }
}
