<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    public const APPROVED = 'APPROVED';

    public const DECLINED = 'DECLINED';

    public const ERROR = 'ERROR';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['number', 'value', 'payment_method', 'status'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
