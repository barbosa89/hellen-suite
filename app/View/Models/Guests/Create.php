<?php

namespace App\View\Models\Guests;

use App\Models\Country;
use App\Constants\Genders;
use App\Models\IdentificationType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

class Create implements Arrayable
{
    private function identificationTypes(): Collection
    {
        return IdentificationType::all(['id', 'type']);
    }

    private function countries(): Collection
    {
        return Country::all(['id', 'name']);
    }

    private function genders(): array
    {
        return Genders::toDictionary();
    }

    public function toArray(): array
    {
        return [
            'identificationTypes' => $this->identificationTypes(),
            'countries' => $this->countries(),
            'genders' => $this->genders(),
        ];
    }
}
