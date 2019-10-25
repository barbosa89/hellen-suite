<?php

use Vinkla\Hashids\Facades\Hashids;

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push(trans('dashboard.dashboard'), route('home'));
});


Breadcrumbs::for('rooms', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('rooms.title'), route('rooms.index'));
});

Breadcrumbs::for('room', function ($trail, $room) {
    $trail->parent('rooms');
    $trail->push($room->number, route('rooms.show', ['id' => Hashids::encode($room->id)]));
});

Breadcrumbs::for('hotels', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('hotels.title'), route('hotels.index'));
});

Breadcrumbs::for('hotel', function ($trail, $hotel) {
    $trail->parent('hotels');
    $trail->push($hotel->business_name, route('hotels.show', ['id' => Hashids::encode($hotel->id)]));
});

Breadcrumbs::for('team', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('team.title'), route('team.index'));
});

Breadcrumbs::for('member', function ($trail, $member) {
    $trail->parent('team');
    $trail->push($member->name, route('hotels.show', ['id' => Hashids::encode($member->id)]));
});

Breadcrumbs::for('invoices', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('invoices.title'), route('invoices.index'));
});

Breadcrumbs::for('invoice', function ($trail, $invoice) {
    $trail->parent('invoices');
    $trail->push($invoice->number, route('invoices.show', ['id' => Hashids::encode($invoice->id)]));
});

Breadcrumbs::for('companies', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('companies.title'), route('companies.index'));
});

Breadcrumbs::for('company', function ($trail, $company) {
    $trail->parent('companies');
    $trail->push($company->business_name, route('company.show', ['id' => Hashids::encode($company->id)]));
});

Breadcrumbs::for('guests', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('guests.title'), route('guests.index'));
});

Breadcrumbs::for('guest', function ($trail, $guest) {
    $trail->parent('guests');
    $trail->push($guest->business_name, route('guest.show', ['id' => Hashids::encode($guest->id)]));
});

Breadcrumbs::for('products', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('products.title'), route('products.index'));
});

Breadcrumbs::for('services', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('services.title'), route('services.index'));
});