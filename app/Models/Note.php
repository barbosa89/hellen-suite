<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use Queryable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content', 'team_member_name', 'team_member_email'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'pivot', 'user_id', 'hotel_id'];

    /**
     * Hashing Product ID.
     *
     * @return string
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    /**
     * The shifts that belong to the note.
     */
    public function shifts()
    {
        return $this->belongsToMany(\App\Models\Shift::class);
    }

    /**
     * The tags that belong to the note.
     */
    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class);
    }

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the hotel that owns the note.
     */
    public function hotel()
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }

    /**
     * Scope a query to get all notes by a hotel and a tag.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\Hotel $hotel
     * @param  \App\Models\Tag $tag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForTag($query, Hotel $hotel, Tag $tag)
    {
        return $query->owner()
            ->ofHotel($hotel->id)
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('id', $tag->id);
            });
    }

    /**
     * Scope a query to get all notes by a hotel.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int $hotelId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfHotel($query, int $hotelId)
    {
        return $query->where('hotel_id', $hotelId);
    }
}
