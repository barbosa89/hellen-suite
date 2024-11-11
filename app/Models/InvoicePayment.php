<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory;

    public const ERROR = 'ERROR';

    public const APPROVED = 'APPROVED';

    public const DECLINED = 'DECLINED';

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
