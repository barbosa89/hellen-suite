<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('hotels/{hotel}/settings', 'HotelSettingController@index')
        ->name('hotels.settings.index')
        ->middleware(['permission:hotels.edit']);
});
