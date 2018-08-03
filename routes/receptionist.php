<?php

Route::group(['middleware' => ['auth', 'role:receptionist']], function() {
    Route::resource('guests', 'GuestController');

    Route::get('invoices', 'InvoiceController@index')
        ->name('invoices.index');

    Route::post('invoices', 'InvoiceController@store')
        ->name('invoices.store');
    
    Route::get('invoices/{id}', 'InvoiceController@show')
        ->name('invoices.show');

    Route::delete('invoices/{id}', 'InvoiceController@destroy')
        ->name('invoices.destroy');

    Route::get('invoices/{id}/rooms', 'InvoiceController@rooms')
        ->name('invoices.rooms');

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

    Route::get('invoices/{id}/products', 'InvoiceController@products')
        ->name('invoices.products');

    Route::post('invoices/{id}/products', 'InvoiceController@addProducts')
        ->name('invoices.products.add');

    Route::get('invoices/{id}/services', 'InvoiceController@services')
        ->name('invoices.services');

    Route::post('invoices/{id}/services', 'InvoiceController@addServices')
        ->name('invoices.services.add');

    Route::post('products/calculate/total', 'ProductController@total')
        ->name('products.total');
    Route::post('services/calculate/total', 'ServiceController@total')
        ->name('services.total');
});
