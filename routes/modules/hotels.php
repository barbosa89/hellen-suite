<?php

Route::group(['middleware' => ['auth', 'role:manager', 'verified']], function() {
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
});