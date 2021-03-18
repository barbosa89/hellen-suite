<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface Chart
{
    public function get(): Collection;
}
