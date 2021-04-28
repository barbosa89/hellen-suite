<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::post('rooms/toggle', 'Api\RoomController@toggle')
		->name('rooms.toggle')
        ->middleware('permission:rooms.toggle');

    Route::post('rooms', 'Api\RoomController@store')
        ->name('rooms.store')
        ->middleware('permission:rooms.create');

    Route::get('rooms/{id}', 'Api\RoomController@show')
		->name('rooms.show')
		->middleware('permission:rooms.show');

    Route::get('hotels/{hotel}/rooms', 'Api\RoomController@index')
        ->name('rooms.index')
        ->middleware('permission:rooms.index');
});
