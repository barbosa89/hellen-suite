<?php

namespace App\Models;

use App\Helpers\Fields;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Shift extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['open', 'cash'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }

    /**
     * The vouchers that belong to the shift.
     */
    public function vouchers()
    {
        return $this->belongsToMany(\App\Models\Voucher::class);
    }

    /**
     * The notes that belong to the shift.
     */
    public function notes()
    {
        return $this->belongsToMany(\App\Models\Note::class);
    }

    /**
     * Get the current shift
     *
     * @param integer $hotel_id
     * @return \App\Models\Shift
     */
    public static function current(int $hotel_id): Shift
    {
        $shift = static::where('open', true)
            ->where('hotel_id', $hotel_id)
            ->where('user_id', id_parent())
            ->first(fields_get('shifts'));

        if (empty($shift)) {
            $shift = self::start($hotel_id);
        }

        return $shift;
    }

    /**
     * Create new Shift
     *
     * @param integer $hotel_id
     * @return \App\Models\Shift
     */
    public static function start(int $hotel_id): Shift
    {
        $shift = new Shift();
        $shift->team_member = auth()->user()->id;
        $shift->team_member_name = auth()->user()->name;
        $shift->user()->associate(id_parent());
        $shift->hotel()->associate($hotel_id);
        $shift->save();

        return $shift;
    }

    /**
     * Scope a query to get open shifts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->where('open', true)
            ->where('team_member', auth()->user()->id)
            ->where('user_id', id_parent());
    }
}
