<?php

namespace App\Models;

use Database\Factories\HotelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $business_name
 * @property string $tin
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $mobile
 * @property string|null $email
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at

 * @mixin \Eloquent
 */
#[Fillable(['business_name', 'tin', 'address', 'phone', 'mobile', 'email', 'image'])]
class Hotel extends Model
{
    /** @use HasFactory<HotelFactory> */
    use HasFactory;
}
