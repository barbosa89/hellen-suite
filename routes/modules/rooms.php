<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::post('rooms/toggle', [RoomController::class, 'toggle'])
		->name('rooms.toggle')
		->middleware('permission:rooms.toggle');

	Route::post('rooms/price', [RoomController::class, 'getPrice'])
		->name('rooms.price')
		->middleware('permission:rooms.index');

	Route::get('rooms/search', [RoomController::class, 'search'])
		->name('rooms.search')
		->middleware('permission:rooms.index');

	Route::delete('rooms/{id}', [RoomController::class, 'destroy'])
		->name('rooms.destroy')
		->middleware('permission:rooms.destroy');

	Route::put('rooms/{id}', [RoomController::class, 'update'])
		->name('rooms.update')
		->middleware('permission:rooms.edit');

	Route::get('rooms/{id}/edit', [RoomController::class, 'edit'])
		->name('rooms.edit')
		->middleware('permission:rooms.edit');

	Route::post('rooms', [RoomController::class, 'store'])
		->name('rooms.store')
		->middleware('permission:rooms.create');

	Route::get('rooms/create', [RoomController::class, 'create'])
		->name('rooms.create')
		->middleware('permission:rooms.create');

	Route::get('rooms/{id}', [RoomController::class, 'show'])
		->name('rooms.show')
		->middleware('permission:rooms.show');

	Route::get('rooms', [RoomController::class, 'index'])
		->name('rooms.index')
		->middleware('permission:rooms.index');
});
