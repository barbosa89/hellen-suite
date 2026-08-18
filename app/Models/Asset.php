<?php

namespace App\Models;

use App\Traits\InteractWithLogs;
use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use HasFactory;
    use InteractWithLogs;
    use LogsActivity;
    use Queryable;

    /**
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * @var array
     */
    protected $hidden = ['id'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function maintenances(): MorphMany
    {
        return $this->morphMany(Maintenance::class, 'maintainable');
    }

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }
}
