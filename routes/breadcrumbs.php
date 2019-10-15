<?php

use Vinkla\Hashids\Facades\Hashids;

// Home
Breadcrumbs::for('home', function ($trail) {
    $trail->push(trans('dashboard.dashboard'), route('home'));
});

// // Home > About
// Breadcrumbs::for('about', function ($trail) {
//     $trail->parent('home');
//     $trail->push('About', route('about'));
// });

// // Home > Blog
// Breadcrumbs::for('blog', function ($trail) {
//     $trail->parent('home');
//     $trail->push('Blog', route('blog'));
// });

// // Home > Blog > [Category]
// Breadcrumbs::for('category', function ($trail, $category) {
//     $trail->parent('blog');
//     $trail->push($category->title, route('category', $category->id));
// });

// // Home > Blog > [Category] > [Post]
// Breadcrumbs::for('post', function ($trail, $post) {
//     $trail->parent('category', $post->category);
//     $trail->push($post->title, route('post', $post->id));
// });

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