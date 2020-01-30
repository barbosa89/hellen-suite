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

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }

    public static function current($hotel_id)
    {
        $shift = static::where('open', true)
            ->where('team_member', auth()->user()->id)
            ->whereHas('hotel', function ($query) use ($hotel_id)
            {
                $query->where('id', $hotel_id);
            })->whereHas('user', function ($query)
            {
                $query->where('id', auth()->user()->parent);
            })->first(Fields::get('shifts'));

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
