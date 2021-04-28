<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::post('rooms/toggle', 'RoomController@toggle')
		->name('rooms.toggle')
		->middleware('permission:rooms.toggle');

	Route::post('rooms/price', 'RoomController@getPrice')
		->name('rooms.price')
		->middleware('permission:rooms.index');

	Route::get('rooms/search', 'RoomController@search')
		->name('rooms.search')
		->middleware('permission:rooms.index');

	Route::delete('rooms/{id}', 'RoomController@destroy')
		->name('rooms.destroy')
		->middleware('permission:rooms.destroy');

	Route::put('rooms/{id}', 'RoomController@update')
		->name('rooms.update')
		->middleware('permission:rooms.edit');

	Route::get('rooms/{id}/edit', 'RoomController@edit')
		->name('rooms.edit')
		->middleware('permission:rooms.edit');

	Route::post('rooms', 'RoomController@store')
		->name('rooms.store')
		->middleware('permission:rooms.create');

	Route::get('rooms/create', 'RoomController@create')
		->name('rooms.create')
		->middleware('permission:rooms.create');

	Route::get('rooms/{id}', 'RoomController@show')
		->name('rooms.show')
		->middleware('permission:rooms.show');

	Route::get('rooms', 'RoomController@index')
		->name('rooms.index')
		->middleware('permission:rooms.index');
});
