<?php

Route::group(['middleware' => ['auth', 'role:receptionist']], function() {
    Route::get('invoices', 'InvoiceController@index')
        ->name('invoices.index');
    Route::post('invoices', 'InvoiceController@store')
        ->name('invoices.store');
    Route::get('invoices/{id}/rooms', 'InvoiceController@addRooms')
        ->name('invoices.rooms.add');
    Route::post('invoices/{id}/rooms', 'InvoiceController@storeRooms')
        ->name('invoices.rooms.store');
});
