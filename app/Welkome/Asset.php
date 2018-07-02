<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use Searchable;
    use LogsActivity;
    
    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }

    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
