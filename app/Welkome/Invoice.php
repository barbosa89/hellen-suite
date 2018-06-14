<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
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

    public function guests()
    {
        return $this->belongsToMany(Welkome\Guest::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Welkome\Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Welkome\Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(Welkome\Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
