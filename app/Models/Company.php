<?php

namespace App\Models;

use App\Traits\Queryable;
use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use Queryable;
    use LogsActivity;
    use InteractWithLogs;

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
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }
}
