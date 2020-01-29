<?php

namespace App\Welkome;

use App\User;
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

    public static function current()
    {
        $user = User::where('id', auth()->user()->id)
            ->whereHas('roles', function ($query)
            {
                $query->where('name', 'receptionist');
            })->with([
                'headquarters' => function($query) {
                    $query->select(['id', 'business_name']);
                }
            ])->first(['id', 'email', 'parent']);

        $shift = static::where('open', true)
            ->where('team_member', $user->id)
            ->whereHas('hotel', function ($query) use ($user)
            {
                $query->where('id', $user->headquarters->first()->id);
            })->whereHas('user', function ($query) use ($user)
            {
                $query->where('id', $user->parent);
            })->first(['id', 'open', 'hotel_id', 'user_id']);

        return $shift;
    }
}
