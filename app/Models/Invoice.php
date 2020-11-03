<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public const PENDING = 'PENDING';

    public const CANCELED = 'CANCELED';

    public const PAID = 'PAID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['number', 'customer_name', 'customer_dni', 'value', 'discount', 'taxes', 'total', 'status'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Models\InvoicePayment::class);
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Currency::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(\App\Models\IdentificationType::class, 'identification_type_id');
    }

    public function plans()
    {
        return $this->belongsToMany(\App\Models\Plan::class);
    }
}
