<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Route::get('invoices/rooms/assign', 'InvoiceController@assign')
    //     ->name('invoices.rooms.assign')
    //     ->middleware(['permission:invoices.create']);

    Route::get('invoices/{id}/additionals/{additional}/remove', 'InvoiceController@destroyAdditional')
        ->name('invoices.additionals.remove')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/additionals', 'InvoiceController@storeAdditional')
        ->name('invoices.additionals.store')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/additionals', 'InvoiceController@createAdditional')
        ->name('invoices.additionals.create')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/vehicles/{vehicle}/guests/{guest}/remove', 'InvoiceController@removeVehicle')
        ->name('invoices.vehicles.remove')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/vehicles/search', 'InvoiceController@searchVehicles')
        ->name('invoices.vehicles.search')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/vehicles/{vehicle}/guests/{guest}', 'InvoiceController@addVehicle')
        ->name('invoices.vehicles.add')
        ->middleware(['permission:invoices.edit']);

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

    Route::post('invoices/multiple', 'InvoiceController@multiple')
        ->name('invoices.room.multiple')
        ->middleware(['permission:invoices.create']);

    Route::post('invoices/{id}/rooms/{room}', 'InvoiceController@changeRoom')
        ->name('invoices.rooms.change')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/rooms/{room}', 'InvoiceController@showFormToChangeRoom')
        ->name('invoices.rooms.change.form')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/rooms', 'InvoiceController@rooms')
        ->name('invoices.rooms')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/rooms', 'InvoiceController@addRooms')
        ->name('invoices.rooms.add')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/guests/{guest}/change', 'InvoiceController@changeGuestRoom')
        ->name('invoices.guests.change')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/guests/{guest}/change', 'InvoiceController@showFormToChangeGuestRoom')
        ->name('invoices.guests.change.form')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/guests/search', 'InvoiceController@searchGuests')
        ->name('invoices.guests.search')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/guests/{guest}', 'InvoiceController@guests')
        ->name('invoices.guests')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/guests/add', 'InvoiceController@addguests')
        ->name('invoices.guests.add')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/guests/{guest}/remove', 'InvoiceController@removeguests')
        ->name('invoices.guests.remove')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/products', 'InvoiceController@products')
        ->name('invoices.products')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/products/{record}/remove', 'InvoiceController@removeProduct')
        ->name('invoices.products.remove')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/products', 'InvoiceController@addProducts')
        ->name('invoices.products.add')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/services/{record}/remove', 'InvoiceController@removeService')
        ->name('invoices.services.remove')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/services', 'InvoiceController@services')
        ->name('invoices.services')
        ->middleware(['permission:invoices.edit']);

    Route::post('invoices/{id}/services', 'InvoiceController@addServices')
        ->name('invoices.services.add')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/companies/search', 'InvoiceController@searchCompanies')
        ->name('invoices.companies.search')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/companies/{company}', 'InvoiceController@addCompanies')
        ->name('invoices.companies.add')
        ->middleware(['permission:invoices.edit']);

    Route::get('invoices/{id}/companies/{company}/remove', 'InvoiceController@removeCompany')
        ->name('invoices.companies.remove')
        ->middleware(['permission:invoices.edit']);
});
