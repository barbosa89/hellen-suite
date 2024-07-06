<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::post('rooms/toggle', [RoomController::class, 'toggle'])
		->name('rooms.toggle')
        ->middleware('permission:rooms.toggle');

    Route::post('rooms', [RoomController::class, 'store'])
        ->name('rooms.store')
        ->middleware('permission:rooms.create');

    Route::get('rooms/{id}', [RoomController::class, 'show'])
		->name('rooms.show')
		->middleware('permission:rooms.show');

    Route::get('hotels/{hotel}/rooms', [RoomController::class, 'index'])
        ->name('rooms.index')
        ->middleware('permission:rooms.index');
});
