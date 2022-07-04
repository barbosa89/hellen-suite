<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
