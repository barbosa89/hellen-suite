<?php

use App\Http\Controllers\GuestController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('guests/{id}/toggle/{voucher}', [GuestController::class, 'toggle'])
        ->name('guests.toggle')
        ->middleware('permission:guests.edit');

    Route::post('vouchers/{id}/guests', [GuestController::class, 'storeForvoucher'])
        ->name('vouchers.guests.store')
        ->middleware(['permission:guests.create', 'open_shift']);

    Route::get('vouchers/{id}/guests/create', [GuestController::class, 'createForvoucher'])
        ->name('vouchers.guests.create')
        ->middleware(['permission:guests.create', 'open_shift']);

    Route::get('guests/export', [GuestController::class, 'export'])
        ->name('guests.export')
        ->middleware(['permission:guests.index']);

    Route::get('guests/search', [GuestController::class, 'search'])
        ->name('guests.search')
        ->middleware(['permission:guests.index']);

    Route::delete('guests/{id}', [GuestController::class, 'destroy'])
        ->name('guests.destroy')
        ->middleware('permission:guests.destroy');

    Route::put('guests/{id}', [GuestController::class, 'update'])
        ->name('guests.update')
        ->middleware('permission:guests.edit');

    Route::get('guests/{id}/edit', [GuestController::class, 'edit'])
        ->name('guests.edit')
        ->middleware('permission:guests.edit');

    Route::post('guests', [GuestController::class, 'store'])
        ->name('guests.store')
        ->middleware('permission:guests.create');

    Route::get('guests/create', [GuestController::class, 'create'])
        ->name('guests.create')
        ->middleware('permission:guests.create');

    Route::get('guests/{id}', [GuestController::class, 'show'])
        ->name('guests.show')
        ->middleware('permission:guests.show');

    Route::get('guests', [GuestController::class, 'index'])
        ->name('guests.index')
        ->middleware('permission:guests.index');
});
