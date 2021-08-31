<?php

namespace App\Traits;

trait Hashable
{
    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = id_encode($this->attributes['id']);
    }
}
