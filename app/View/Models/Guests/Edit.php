<?php

namespace App\View\Models\Guests;

use App\Models\Guest;
use App\Models\Country;
use App\Constants\Genders;
use Illuminate\Support\Arr;
use App\Models\IdentificationType;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

class Edit implements Arrayable
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    private function guest(): Guest
    {
        $guest = Guest::owner()
            ->where('id', $this->id)
            ->firstOrFail(fields_get('guests'));

        $guest->load([
            'identificationType' => function ($query) {
                $query->select(['id', 'type']);
            },
            'country' => function ($query) {
                $query->select(['id', 'name']);
            }
        ]);

        return $guest;
    }

    private function identificationTypes(IdentificationType $except): Collection
    {
        return IdentificationType::where('id', '!=', $except->id)
            ->get(['id', 'type']);
    }

    private function countries(Country $except): Collection
    {
        return Country::where('id', '!=', $except->id)
            ->get(['id', 'name']);
    }

    private function genders(Guest $guest): array
    {
        $genders = Genders::toDictionary();

        return Arr::except($genders, [$guest->gender]);
    }

    public function toArray(): array
    {
        $guest = $this->guest();

        return [
            'guest' => $guest,
            'identificationTypes' => $this->identificationTypes($guest->identificationType),
            'countries' => $this->countries($guest->country),
            'genders' => $this->genders($guest),
        ];
    }
}
