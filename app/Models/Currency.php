<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    public const COP = 'COP';

    public const USD = 'USD';

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }
}
