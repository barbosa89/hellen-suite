<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('guests/{id}/toggle/{invoice}', 'GuestController@toggle')
        ->name('guests.toggle')
        ->middleware('permission:guests.edit');

    Route::post('invoices/{id}/guests', 'GuestController@storeForInvoice')
        ->name('invoices.guests.store')
        ->middleware(['permission:guests.create']);

    Route::get('invoices/{id}/guests/create', 'GuestController@createForInvoice')
        ->name('invoices.guests.create')
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
