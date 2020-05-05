<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The shifts that belong to the note.
     */
    public function shifts()
    {
        return $this->belongsToMany(\App\Welkome\Shift::class);
    }

    /**
     * The tags that belong to the note.
     */
    public function tags()
    {
        return $this->belongsToMany(\App\Welkome\Tag::class);
    }
}
