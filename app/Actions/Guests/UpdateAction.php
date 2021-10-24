<?php

namespace App\Actions\Guests;

use App\Actions\Action;

class UpdateAction extends Action
{
    public function handle(): bool
    {
        $this->model->dni = $this->data['dni'];
        $this->model->name = $this->data['name'];
        $this->model->last_name = $this->data['last_name'];
        $this->model->email = $this->data['email'] ?? null;
        $this->model->phone = $this->data['phone'] ?? null;
        $this->model->gender = $this->data['gender'] ?? null;
        $this->model->address = $this->data['address'] ?? null;
        $this->model->birthdate = $this->data['birthdate'] ?? null;
        $this->model->profession = $this->data['profession'] ?? null;
        $this->model->country()->associate($this->data['country_id']);
        $this->model->identificationType()->associate($this->data['identification_type_id']);

        return $this->model->save();
    }
}
