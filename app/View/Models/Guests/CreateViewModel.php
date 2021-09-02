<?php

namespace App\View\Models\Guests;

use App\Constants\Genders;
use App\Services\CountryCache;
use Illuminate\Support\Collection;
use App\Services\IdentificationTypeCache;
use Illuminate\Contracts\Support\Arrayable;

class CreateViewModel implements Arrayable
{
    private function identificationTypes(): Collection
    {
        return (new IdentificationTypeCache())->get();
    }

    private function countries(): Collection
    {
        return (new CountryCache())->get();
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
