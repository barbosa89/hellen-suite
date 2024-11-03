<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherController;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('vouchers/{id}/rooms/{room}/deliver', [VoucherController::class, 'deliverRoom'])
        ->name('vouchers.rooms.deliver')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/process', [VoucherController::class, 'process'])
        ->name('vouchers.process')
        ->middleware(['permission:vouchers.edit']);

    Route::get('vouchers/process', [VoucherController::class, 'showFormToProcess'])
        ->name('vouchers.process.form')
        ->middleware(['permission:vouchers.edit']);

    Route::post('vouchers/{id}/external', [VoucherController::class, 'storeExternalService'])
        ->name('vouchers.external.store')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/external', [VoucherController::class, 'addExternalService'])
        ->name('vouchers.external.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/payments/close', [VoucherController::class, 'closePayment'])
        ->name('vouchers.payments.close')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/export', [VoucherController::class, 'export'])
        ->name('vouchers.export')
        ->middleware(['permission:vouchers.show']);

    Route::post('vouchers/{id}/reservation/checkin', [VoucherController::class, 'storeReservationCheckin'])
        ->name('vouchers.reservation.checkin.store')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/reservation/checkin', [VoucherController::class, 'takeReservationCheckin'])
        ->name('vouchers.reservation.checkin')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/losses', [VoucherController::class, 'registerAsLoss'])
        ->name('vouchers.losses')
        ->middleware(['permission:vouchers.losses', 'open_shift']);

    Route::post('vouchers/{id}/close', [VoucherController::class, 'close'])
        ->name('vouchers.close')
        ->middleware(['permission:vouchers.close', 'open_shift']);

    Route::get('vouchers/{id}/additionals/{additional}/remove', [VoucherController::class, 'destroyAdditional'])
        ->name('vouchers.additionals.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/additionals', [VoucherController::class, 'storeAdditional'])
        ->name('vouchers.additionals.store')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/additionals', [VoucherController::class, 'createAdditional'])
        ->name('vouchers.additionals.create')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/vehicles/{vehicle}/guests/{guest}/remove', [VoucherController::class, 'removeVehicle'])
        ->name('vouchers.vehicles.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/vehicles/search', [VoucherController::class, 'searchVehicles'])
        ->name('vouchers.vehicles.search')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/vehicles/{vehicle}/guests/{guest}', [VoucherController::class, 'addVehicle'])
        ->name('vouchers.vehicles.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers', [VoucherController::class, 'index'])
        ->name('vouchers.index')
        ->middleware(['permission:vouchers.index']);

    Route::get('vouchers/search', [VoucherController::class, 'search'])
        ->name('vouchers.search')
        ->middleware(['permission:vouchers.index']);

    Route::get('vouchers/create', [VoucherController::class, 'create'])
        ->name('vouchers.create')
        ->middleware(['permission:vouchers.create']);

    Route::post('vouchers', [VoucherController::class, 'store'])
        ->name('vouchers.store')
        ->middleware(['permission:vouchers.create']);

    Route::get('vouchers/{id}', [VoucherController::class, 'show'])
        ->name('vouchers.show')
        ->middleware(['permission:vouchers.show']);

    Route::delete('vouchers/{id}', [VoucherController::class, 'destroy'])
        ->name('vouchers.destroy')
        ->middleware(['permission:vouchers.destroy', 'open_shift']);

    Route::post('vouchers/multiple', [VoucherController::class, 'createWithMultipleRooms'])
        ->name('vouchers.room.multiple')
        ->middleware(['permission:vouchers.create']);

    Route::post('vouchers/{id}/rooms/{room}', [VoucherController::class, 'changeRoom'])
        ->name('vouchers.rooms.change')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/rooms/{room}', [VoucherController::class, 'showFormToChangeRoom'])
        ->name('vouchers.rooms.change.form')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/rooms', [VoucherController::class, 'showFormToAddRooms'])
        ->name('vouchers.rooms')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/rooms', [VoucherController::class, 'addRooms'])
        ->name('vouchers.rooms.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/guests/{guest}/change', [VoucherController::class, 'changeGuestRoom'])
        ->name('vouchers.guests.change')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/guests/{guest}/change', [VoucherController::class, 'showFormToChangeGuestRoom'])
        ->name('vouchers.guests.change.form')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/guests/search', [VoucherController::class, 'searchGuests'])
        ->name('vouchers.guests.search')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/guests/{guest}', [VoucherController::class, 'showFormToAddGuests'])
        ->name('vouchers.guests')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/guests/add', [VoucherController::class, 'addGuests'])
        ->name('vouchers.guests.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/guests/{guest}/remove', [VoucherController::class, 'removeGuests'])
        ->name('vouchers.guests.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/products', [VoucherController::class, 'products'])
        ->name('vouchers.products')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/products/{record}/remove', [VoucherController::class, 'removeProduct'])
        ->name('vouchers.products.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/products', [VoucherController::class, 'addProducts'])
        ->name('vouchers.products.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/services/{record}/remove', [VoucherController::class, 'removeService'])
        ->name('vouchers.services.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/services/{type?}', [VoucherController::class, 'showFormToAddServices'])
        ->name('vouchers.services')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::post('vouchers/{id}/services', [VoucherController::class, 'addServices'])
        ->name('vouchers.services.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/companies/search', [VoucherController::class, 'searchCompanies'])
        ->name('vouchers.companies.search')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/companies/{company}', [VoucherController::class, 'addCompanies'])
        ->name('vouchers.companies.add')
        ->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/companies/{company}/remove', [VoucherController::class, 'removeCompany'])
        ->name('vouchers.companies.remove')
        ->middleware(['permission:vouchers.edit', 'open_shift']);
});
