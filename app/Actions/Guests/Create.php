<?php

namespace App\Actions\Guests;

use App\Models\Guest;
use App\Actions\Action;

class Create extends Action
{
    public function execute(): Guest
    {
        $guest = new Guest();
        $guest->name = $this->data['name'];
        $guest->last_name = $this->data['last_name'];
        $guest->dni = $this->data['dni'];
        $guest->email = $this->data['email'] ?? null;
        $guest->address = $this->data['address'] ?? null;
        $guest->phone = $this->data['phone'] ?? null;
        $guest->gender = $this->data['gender'] ?? null;
        $guest->birthdate = $this->data['birthdate'] ?? null;
        $guest->profession = $this->data['profession'] ?? null;
        $guest->user()->associate(id_parent());
        $guest->country()->associate($this->data['country_id']);
        $guest->identificationType()->associate($this->data['identification_type_id']);
        $guest->save();

        return $guest;
    }
}
