<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Route::get('invoices/rooms/assign', 'InvoiceController@assign')
    //     ->name('invoices.rooms.assign')
    //     ->middleware(['permission:invoices.create']);

	Route::post('invoices/multiple', 'InvoiceController@multiple')
        ->name('invoices.room.multiple')
        ->middleware(['permission:invoices.create']);

    Route::get('invoices', 'InvoiceController@index')
        ->name('invoices.index')
        ->middleware(['permission:invoices.index']);

    Route::get('invoices/search', 'InvoiceController@search')
        ->name('invoices.search')
        ->middleware(['permission:invoices.index']);

    Route::get('invoices/create', 'InvoiceController@create')
        ->name('invoices.create')
        ->middleware(['permission:invoices.create']);

    Route::post('invoices', 'InvoiceController@store')
        ->name('invoices.store')
        ->middleware(['permission:invoices.create']);

    Route::get('invoices/{id}', 'InvoiceController@show')
        ->name('invoices.show')
        ->middleware(['permission:invoices.show']);

    Route::delete('invoices/{id}', 'InvoiceController@destroy')
        ->name('invoices.destroy')
        ->middleware(['permission:invoices.destroy']);

    Route::get('invoices/{id}/rooms', 'InvoiceController@rooms')
        ->name('invoices.rooms')
        ->middleware(['permission:invoices.index']);

    Route::post('invoices/{id}/rooms', 'InvoiceController@addRooms')
        ->name('invoices.rooms.add');

    Route::get('invoices/{id}/guests/search', 'InvoiceController@searchGuests')
        ->name('invoices.guests.search');

    Route::post('invoices/{id}/guests', 'InvoiceController@storeGuests')
        ->name('invoices.guests.store');

    Route::get('invoices/{id}/guests/create', 'InvoiceController@createGuests')
        ->name('invoices.guests.create');

    Route::get('invoices/{id}/guests/{guest}', 'InvoiceController@guests')
        ->name('invoices.guests');

    Route::post('invoices/{id}/guests/add', 'InvoiceController@addguests')
        ->name('invoices.guests.add');

    Route::post('invoices/{id}/guests/remove', 'InvoiceController@removeguests')
        ->name('invoices.guests.remove');

    Route::get('invoices/{id}/products', 'InvoiceController@products')
        ->name('invoices.products');

    Route::post('invoices/{id}/products', 'InvoiceController@addProducts')
        ->name('invoices.products.add');

    Route::get('invoices/{id}/services', 'InvoiceController@services')
        ->name('invoices.services');

    Route::post('invoices/{id}/services', 'InvoiceController@addServices')
        ->name('invoices.services.add');

    Route::get('invoices/{id}/companies/search', 'InvoiceController@searchCompanies')
        ->name('invoices.companies.search');

    Route::get('invoices/{id}/companies/create', 'InvoiceController@createcompanies')
        ->name('invoices.companies.create');

    Route::get('invoices/{id}/companies/{company}', 'InvoiceController@addCompanies')
        ->name('invoices.companies.add');

    Route::post('invoices/{id}/companies', 'InvoiceController@storecompanies')
        ->name('invoices.companies.store');
});
