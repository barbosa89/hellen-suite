<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('guests/{id}/toggle/{voucher}', 'GuestController@toggle')
        ->name('guests.toggle')
        ->middleware('permission:guests.edit');

    Route::post('vouchers/{id}/guests', 'GuestController@storeForvoucher')
        ->name('vouchers.guests.store')
        ->middleware(['permission:guests.create']);

    Route::get('vouchers/{id}/guests/create', 'GuestController@createForvoucher')
        ->name('vouchers.guests.create')
        ->middleware(['permission:guests.create']);

	Route::get('guests/export', 'GuestController@export')
		->name('guests.export')
		->middleware(['permission:guests.index']);

    Route::post('guests/search/unregistered', 'GuestController@searchUnregistered')
        ->name('guests.search.unregistered')
        ->middleware(['permission:guests.index']);

    Route::get('guests/search', 'GuestController@search')
        ->name('guests.search')
        ->middleware(['permission:guests.index']);

	Route::delete('guests/{id}', 'GuestController@destroy')
		->name('guests.destroy')
		->middleware('permission:guests.destroy');

	Route::put('guests/{id}', 'GuestController@update')
		->name('guests.update')
		->middleware('permission:guests.edit');

	Route::get('guests/{id}/edit', 'GuestController@edit')
		->name('guests.edit')
		->middleware('permission:guests.edit');

	Route::post('guests', 'GuestController@store')
		->name('guests.store')
		->middleware('permission:guests.create');

	Route::get('guests/create', 'GuestController@create')
		->name('guests.create')
		->middleware('permission:guests.create');

	Route::get('guests/{id}', 'GuestController@show')
		->name('guests.show')
		->middleware('permission:guests.show');

	Route::get('guests', 'GuestController@index')
		->name('guests.index')
		->middleware('permission:guests.index');
});
