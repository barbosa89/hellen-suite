<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'in_at',
        'out_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'in_at' => 'datetime',
        'out_at' => 'datetime',
    ];

    public function voucher()
    {
        return $this->belongsTo(\App\Models\Voucher::class);
    }

    public function guest()
    {
        return $this->belongsTo(\App\Models\Guest::class);
    }
}
