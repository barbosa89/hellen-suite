<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use Sluggable;
    use HasFactory;

    public const CHECK_IN = 'check-in';
    public const CHECK_OUT = 'check-out';
    public const VEHICLE = 'vehicle';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['description', 'slug'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['hash', 'value'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'pivot', 'user_id'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'description'
            ]
        ];
    }

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
     * Add value attribute.
     *
     * @return string
     */
    public function getValueAttribute()
    {
        return $this->attributes['value'] = strtolower($this->attributes['description']);
    }

    /**
     * The notes that belong to the tag.
     */
    public function notes()
    {
        return $this->belongsToMany(\App\Models\Note::class);
    }

    /**
     * Get the user that owns the tag.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
