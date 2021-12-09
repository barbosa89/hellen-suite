<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified', 'role:manager']], function() {
	Route::get('hotels/{hotel}/settings', 'HotelSettingController@index')
        ->name('hotels.settings.index');
});
