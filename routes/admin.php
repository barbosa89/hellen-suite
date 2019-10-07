<?php

Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::get('assets/{id}/assign', 'AssetController@assign')
        ->name('assets.assign');

    Route::resource('assets', 'AssetController');

    Route::post('products/{id}/increase', 'ProductController@increase')
        ->name('products.increase');

    Route::get('products/{id}/increase', 'ProductController@showIncreaseForm')
        ->name('products.increase.form');

    Route::resource('products', 'ProductController');

    Route::resource('receptionists', 'ReceptionistController');

	Route::resource('services', 'ServiceController');

    # Hotels module

	Route::get('hotels/search', 'HotelController@search')
		->name('hotels.search');

	Route::delete('hotels/{id}', 'HotelController@destroy')
		->name('hotels.destroy');

	Route::put('hotels/{id}', 'HotelController@update')
		->name('hotels.update');

	Route::get('hotels/{id}/toggle', 'HotelController@toggle')
		->name('hotels.toggle');

	Route::get('hotels/{id}/edit', 'HotelController@edit')
		->name('hotels.edit');

	Route::post('hotels', 'HotelController@store')
		->name('hotels.store');

	Route::get('hotels/create', 'HotelController@create')
		->name('hotels.create');

	Route::get('hotels/{id}', 'HotelController@show')
		->name('hotels.show');

	Route::get('hotels', 'HotelController@index')
		->name('hotels.index');

    # Team module

	Route::get('team/search', 'TeamController@search')
		->name('team.search');

	Route::delete('team/{id}', 'TeamController@destroy')
		->name('team.destroy');

	Route::put('team/{id}', 'TeamController@update')
		->name('team.update');

	Route::get('team/{id}/toggle', 'TeamController@toggle')
		->name('team.toggle');

	Route::get('team/{id}/edit', 'TeamController@edit')
		->name('team.edit');

	Route::post('team', 'TeamController@store')
		->name('team.store');

	Route::get('team/create', 'TeamController@create')
		->name('team.create');

	Route::get('team/{id}', 'TeamController@show')
		->name('team.show');

	Route::get('team', 'TeamController@index')
		->name('team.index');

    # Rooms module

	Route::get('rooms/search', 'RoomController@search')
		->name('rooms.search');

	Route::delete('rooms/{id}', 'RoomController@destroy')
		->name('rooms.destroy');

	Route::put('rooms/{id}', 'RoomController@update')
		->name('rooms.update');

	Route::get('rooms/{id}/edit', 'RoomController@edit')
		->name('rooms.edit');

	Route::post('rooms', 'RoomController@store')
		->name('rooms.store');

	Route::get('rooms/create', 'RoomController@create')
		->name('rooms.create');

	Route::get('rooms/{id}', 'RoomController@show')
		->name('rooms.show');

	Route::get('rooms', 'RoomController@index')
		->name('rooms.index');
});