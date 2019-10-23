<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function citizens()
    {
        return $this->hasMany(\App\Welkome\Guest::class);
    }
}
