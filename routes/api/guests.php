<?php

use Illuminate\Support\Facades\Route;

Route::post('guests', 'Api\GuestController@store')
    ->name('guests.create')
    ->middleware('permission:guests.create');

Route::get('guests', 'Api\GuestController@index')
    ->name('guests.index')
    ->middleware('permission:guests.index');
