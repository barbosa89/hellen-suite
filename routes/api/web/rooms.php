<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('hotels/{hotel}/rooms', 'Api\RoomController@index')
		->name('rooms.index');
});
