<?php

use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

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
    $trail->push($room->number, route('rooms.show', ['id' => id_encode($room->id)]));
});

Breadcrumbs::for('hotels', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('hotels.title'), route('hotels.index'));
});

Breadcrumbs::for('hotel', function ($trail, $hotel) {
    $trail->parent('hotels');
    $trail->push($hotel->business_name, route('hotels.show', ['id' => id_encode($hotel->id)]));
});

Breadcrumbs::for('team', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('team.title'), route('team.index'));
});

Breadcrumbs::for('member', function ($trail, $member) {
    $trail->parent('team');
    $trail->push($member->name, route('hotels.show', ['id' => id_encode($member->id)]));
});

Breadcrumbs::for('vouchers', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('vouchers.title'), route('vouchers.index'));
});

Breadcrumbs::for('process', function ($trail) {
    $trail->parent('vouchers');
    $trail->push(trans('vouchers.process'), route('vouchers.process.form'));
});

Breadcrumbs::for('voucher', function ($trail, $voucher) {
    $trail->parent('vouchers');
    $trail->push($voucher->number, route('vouchers.show', ['id' => id_encode($voucher->id)]));
});

Breadcrumbs::for('payments', function ($trail, $voucher) {
    $trail->parent('voucher', $voucher);
    $trail->push(trans('payments.title'), route('vouchers.show', ['id' => id_encode($voucher->id)]));
});

Breadcrumbs::for('companies', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('companies.title'), route('companies.index'));
});

Breadcrumbs::for('company', function ($trail, $company) {
    $trail->parent('companies');
    $trail->push($company->business_name, route('companies.show', ['id' => id_encode($company->id)]));
});

Breadcrumbs::for('guests', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('guests.title'), route('guests.index'));
});

Breadcrumbs::for('guest', function ($trail, $guest) {
    $trail->parent('guests');
    $trail->push($guest->full_name, route('guests.show', ['id' => id_encode($guest->id)]));
});

Breadcrumbs::for('products', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('products.title'), route('products.index'));
});

Breadcrumbs::for('product', function ($trail, $product) {
    $trail->parent('products');
    $trail->push($product->description, route('products.show', ['id' => id_encode($product->id)]));
});

Breadcrumbs::for('product-transactions', function ($trail) {
    $trail->parent('products');
    $trail->push(trans('transactions.title'), route('products.index'));
});

Breadcrumbs::for('services', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('services.title'), route('services.index'));
});

Breadcrumbs::for('service', function ($trail, $service) {
    $trail->parent('services');
    $trail->push($service->description, route('services.show', ['id' => id_encode($service->id)]));
});

Breadcrumbs::for('dining', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('dining.title'), route('dining.index'));
});

Breadcrumbs::for('dining-service', function ($trail, $service) {
    $trail->parent('dining');
    $trail->push($service->description, route('dining.show', ['id' => id_encode($service->id)]));
});

Breadcrumbs::for('assets', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('assets.title'), route('assets.index'));
});

Breadcrumbs::for('asset', function ($trail, $asset) {
    $trail->parent('assets');
    $trail->push($asset->description, route('assets.show', ['id' => id_encode($asset->id)]));
});

Breadcrumbs::for('props', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('props.title'), route('props.index'));
});

Breadcrumbs::for('prop', function ($trail, $prop) {
    $trail->parent('props');
    $trail->push($prop->description, route('props.show', ['id' => id_encode($prop->id)]));
});

Breadcrumbs::for('replicate prop', function ($trail) {
    $trail->parent('props');
    $trail->push('Replicar', route('props.index'));
});

Breadcrumbs::for('vehicles', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('vehicles.title'), route('vehicles.index'));
});

Breadcrumbs::for('vehicle', function ($trail, $vehicle) {
    $trail->parent('vehicles');
    $trail->push($vehicle->registration, route('vehicles.show', ['id' => id_encode($vehicle->id)]));
});

Breadcrumbs::for('shifts', function ($trail) {
    $trail->parent('home');
    $trail->push(trans('shifts.title'), route('shifts.index'));
});

Breadcrumbs::for('shift', function ($trail, $shift) {
    $trail->parent('shifts');
    $trail->push($shift->hotel->business_name, route('shifts.show', ['id' => id_encode($shift->id)]));
});
