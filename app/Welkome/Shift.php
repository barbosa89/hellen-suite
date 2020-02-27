<?php

namespace App\Welkome;

use App\Helpers\{Fields, Id};
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Shift extends Model
{
    use LogsActivity;

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function vouchers()
    {
        return $this->belongsToMany(\App\Welkome\Voucher::class);
    }

    public static function current($hotel_id)
    {
        $shift = static::where('open', true)
            ->where('team_member', auth()->user()->id)
            ->where('hotel_id', $hotel_id)
            ->where('user_id', Id::parent())
            ->first(Fields::get('shifts'));

        if (empty($shift)) {
            $shift = self::start($hotel_id);
        }

        return $shift;
    }

    public static function start($hotel_id)
    {
        $shift = new Shift();
        $shift->team_member = auth()->user()->id;
        $shift->user()->associate(Id::parent());
        $shift->hotel()->associate($hotel_id);
        $shift->save();

        return $shift;
    }
}
