<?php

namespace App\Contracts;

interface Handleable
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|bool
     */
    public function handle();
}
