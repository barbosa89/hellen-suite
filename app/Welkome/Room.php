<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'number', 'description', 'price', 'status', 'user_id', 'tax_included', 'is_suite', 'capacity', 'floors'
    ];

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }

    public function assets()
    {
        return $this->belongsToMany(\App\Welkome\Asset::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Welkome\Product::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }
}
