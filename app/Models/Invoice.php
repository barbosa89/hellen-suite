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

    /**
     * Scope a query to select all columns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectAll($query)
    {
        return $query->select([
            'id',
            'number',
            'customer_name',
            'customer_dni',
            'value',
            'discount',
            'taxes',
            'total',
            'status',
            'identification_type_id',
            'currency_id',
            'user_id',
            'created_at',
            'updated_at'
        ]);
    }

    /**
     * Scope a query to owner user invoices.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
