<?php

Route::group(['middleware' => ['auth', 'role:receptionist']], function() {
    Route::get('invoices', 'InvoiceController@index')
        ->name('invoices.index');

    Route::post('invoices', 'InvoiceController@store')
        ->name('invoices.store');
    
    Route::get('invoices/{id}', 'InvoiceController@show')
        ->name('invoices.show');

    // ...

    Route::get('invoices/{id}/rooms', 'InvoiceController@addRooms')
        ->name('invoices.rooms.add');

    Route::post('invoices/{id}/rooms', 'InvoiceController@storeRooms')
        ->name('invoices.rooms.store');

    Route::get('invoices/{id}/guests/search', 'InvoiceController@searchGuests')
        ->name('invoices.guests.search');

    // Route::get('invoices/{id}/guests', 'InvoiceController@registerGuests')
    //     ->name('invoices.guests.register');

    Route::post('invoices/{id}/guests', 'InvoiceController@storeGuests')
        ->name('invoices.guests.store');

    Route::get('invoices/{id}/guests/create', 'InvoiceController@createGuests')
        ->name('invoices.guests.create');        
});
