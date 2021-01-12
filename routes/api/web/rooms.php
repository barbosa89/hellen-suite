<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
    Route::post('rooms', 'Api\RoomController@store')
        ->name('rooms.store')
        ->middleware('permission:rooms.create');

    Route::get('hotels/{hotel}/rooms', 'Api\RoomController@index')
        ->name('rooms.index')
        ->middleware('permission:rooms.index');
});
