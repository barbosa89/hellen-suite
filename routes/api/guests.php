<?php

use Illuminate\Support\Facades\Route;

Route::get('guests', 'Api\GuestController@index')
    ->name('guests.index')
    ->middleware('permission:guests.index');
