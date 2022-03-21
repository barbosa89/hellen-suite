<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Configuration extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }
}
