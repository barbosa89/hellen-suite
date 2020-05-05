<?php

namespace App\Welkome;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use Sluggable;

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
    protected $hidden = ['id', 'pivot', 'description', 'user_id'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
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
        return $this->belongsToMany(\App\Welkome\Note::class);
    }

    /**
     * Get the user that owns the tag.
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
