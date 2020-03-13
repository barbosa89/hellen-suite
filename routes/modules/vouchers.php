<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('vouchers/{id}/rooms/{room}/deliver', 'VoucherController@deliverRoom')
        ->name('vouchers.rooms.deliver')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/process', 'VoucherController@process')
        ->name('vouchers.process')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/process', 'VoucherController@showFormToProcess')
        ->name('vouchers.process.form')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/external', 'VoucherController@storeExternalService')
        ->name('vouchers.external.store')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/external', 'VoucherController@addExternalService')
        ->name('vouchers.external.add')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/payments/close', 'VoucherController@closePayment')
        ->name('vouchers.payments.close')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/export', 'VoucherController@export')
        ->name('vouchers.export')
        ->middleware(['permission:vouchers.show']);

    Route::post('vouchers/{id}/reservation/checkin', 'VoucherController@storeReservationCheckin')
        ->name('vouchers.reservation.checkin.store')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/reservation/checkin', 'VoucherController@takeReservationCheckin')
        ->name('vouchers.reservation.checkin')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/losses', 'VoucherController@registerAsLoss')
        ->name('vouchers.losses')
        ->middleware(['permission:vouchers.losses']);

    Route::post('vouchers/{id}/close', 'VoucherController@close')
        ->name('vouchers.close')
        ->middleware(['permission:vouchers.close']);

    Route::get('vouchers/{id}/additionals/{additional}/remove', 'VoucherController@destroyAdditional')
        ->name('vouchers.additionals.remove')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/additionals', 'VoucherController@storeAdditional')
        ->name('vouchers.additionals.store')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/additionals', 'VoucherController@createAdditional')
        ->name('vouchers.additionals.create')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/vehicles/{vehicle}/guests/{guest}/remove', 'VoucherController@removeVehicle')
        ->name('vouchers.vehicles.remove')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/vehicles/search', 'VoucherController@searchVehicles')
        ->name('vouchers.vehicles.search')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/vehicles/{vehicle}/guests/{guest}', 'VoucherController@addVehicle')
        ->name('vouchers.vehicles.add')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers', 'VoucherController@index')
        ->name('vouchers.index')
        ->middleware(['permission:vouchers.index']);

    Route::get('vouchers/search', 'VoucherController@search')
        ->name('vouchers.search')
        ->middleware(['permission:vouchers.index']);

    Route::get('vouchers/create', 'VoucherController@create')
        ->name('vouchers.create')
        ->middleware(['permission:vouchers.create']);

    Route::post('vouchers', 'VoucherController@store')
        ->name('vouchers.store')
        ->middleware(['permission:vouchers.create']);

    Route::get('vouchers/{id}', 'VoucherController@show')
        ->name('vouchers.show')
        ->middleware(['permission:vouchers.show']);

    Route::delete('vouchers/{id}', 'VoucherController@destroy')
        ->name('vouchers.destroy')
        ->middleware(['permission:vouchers.destroy']);

    Route::post('vouchers/multiple', 'VoucherController@createWithMultipleRooms')
        ->name('vouchers.room.multiple')
        ->middleware(['permission:vouchers.create']);

    Route::post('vouchers/{id}/rooms/{room}', 'VoucherController@changeRoom')
        ->name('vouchers.rooms.change')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/rooms/{room}', 'VoucherController@showFormToChangeRoom')
        ->name('vouchers.rooms.change.form')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/rooms', 'VoucherController@showFormToAddRooms')
        ->name('vouchers.rooms')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/rooms', 'VoucherController@addRooms')
        ->name('vouchers.rooms.add')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/guests/{guest}/change', 'VoucherController@changeGuestRoom')
        ->name('vouchers.guests.change')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/guests/{guest}/change', 'VoucherController@showFormToChangeGuestRoom')
        ->name('vouchers.guests.change.form')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/guests/search', 'VoucherController@searchGuests')
        ->name('vouchers.guests.search')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/guests/{guest}', 'VoucherController@showFormToAddGuests')
        ->name('vouchers.guests')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/guests/add', 'VoucherController@addGuests')
        ->name('vouchers.guests.add')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/guests/{guest}/remove', 'VoucherController@removeGuests')
        ->name('vouchers.guests.remove')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/products', 'VoucherController@products')
        ->name('vouchers.products')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/products/{record}/remove', 'VoucherController@removeProduct')
        ->name('vouchers.products.remove')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/products', 'VoucherController@addProducts')
        ->name('vouchers.products.add')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/services/{record}/remove', 'VoucherController@removeService')
        ->name('vouchers.services.remove')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/services/{type?}', 'VoucherController@showFormToAddServices')
        ->name('vouchers.services')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/services', 'VoucherController@addServices')
        ->name('vouchers.services.add')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/companies/search', 'VoucherController@searchCompanies')
        ->name('vouchers.companies.search')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/companies/{company}', 'VoucherController@addCompanies')
        ->name('vouchers.companies.add')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/{id}/companies/{company}/remove', 'VoucherController@removeCompany')
        ->name('vouchers.companies.remove')
        ->middleware(['permission:vouchers.edit']);
});
