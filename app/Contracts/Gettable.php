<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface Gettable
{
    public function get(): Collection;
}
