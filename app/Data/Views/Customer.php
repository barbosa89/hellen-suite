<?php

namespace App\Data\Views;

use App\Models\Guest;
use App\Models\Voucher;
use App\Contracts\Buildable;
use Illuminate\Contracts\Support\Arrayable;

class Customer implements Arrayable, Buildable
{
    public string $name = '';
    public string $tin = '';
    public string $route = '';
    public string $email = '';
    public string $address = '';
    public string $phone = '';
    private Voucher $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher = $voucher;
    }

    public function build(): self
    {
        if ($this->hasCompany()) {
            $this->buildFromCompany();
        } else {
            $this->tryFromGuests();
        }

        return $this;
    }

    private function hasCompany(): bool
    {
        return !empty($this->voucher->company);
    }

    private function hasGuests(): bool
    {
        return $this->voucher->guests->isNotEmpty();
    }

    private function buildFromCompany(): void
    {
        $this->name = $this->voucher->company->business_name;
        $this->tin = $this->voucher->company->tin;
        $this->route = route('companies.show', ['id' => $this->voucher->company->hash]);
        $this->email = $this->voucher->company->email ?? '';
        $this->address = $this->voucher->company->address ?? '';
        $this->phone = $this->voucher->company->phone ?? '';
    }

    private function tryFromGuests(): void
    {
        if ($this->hasGuests()) {
            /** @var Guest $guest */
            $guest = $this->voucher->guests->first(function ($guest) {
                return (bool) $guest->pivot->main;
            });

            if ($guest) {
                $this->name = $guest->full_name;
                $this->tin = $guest->dni;
                $this->route = route('guests.show', ['id' => $guest->hash]);
                $this->email = $guest->email ?? '';
                $this->address = $guest->address ?? '';
                $this->phone = $guest->phone ?? '';
            }
        }
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'tin' => $this->tin,
            'route' => $this->route,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
        ];
    }
}
