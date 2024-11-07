<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::delete('vouchers/{voucher}/payments/{id}', [PaymentController::class, 'destroy'])
        ->name('payments.destroy')
        ->middleware('permission:payments.destroy');

    Route::put('vouchers/{voucher}/payments/{id}', [PaymentController::class, 'update'])
        ->name('payments.update')
        ->middleware('permission:payments.edit');

    Route::get('vouchers/{voucher}/payments/{id}/edit', [PaymentController::class, 'edit'])
        ->name('payments.edit')
        ->middleware('permission:payments.edit');

    Route::post('payments/{voucher}', [PaymentController::class, 'store'])
        ->name('payments.store')
        ->middleware('permission:payments.create');

    Route::get('vouchers/{voucher}/payments/create', [PaymentController::class, 'create'])
        ->name('payments.create')
        ->middleware('permission:payments.create');

    Route::get('vouchers/{voucher}/payments', [PaymentController::class, 'index'])
        ->name('payments.index')
        ->middleware('permission:payments.index');
});
