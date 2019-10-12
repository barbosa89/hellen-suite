<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// TODO: Analizar el nombre de las siguientes rutas
	Route::get('rooms/list/assign/{id}', 'RoomController@assign')
		->name('rooms.assign');

	Route::post('rooms/pool', 'RoomController@pool')
		->name('rooms.pool');

	// TODO: Anular las siguientes dos rutas
	Route::get('rooms/list/{id}', 'RoomController@display')
		->name('rooms.display');

	Route::get('rooms/list', 'RoomController@list')
		->name('rooms.list');

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