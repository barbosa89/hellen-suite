<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VoucherController;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('hotels/{hotel}/vouchers/datasets/guests/{period}', [VoucherController::class, 'getGuestDataset'])
		->name('vouchers.datasets.guests')
		->middleware('permission:vouchers.index');

    Route::get('hotels/{hotel}/vouchers', [VoucherController::class, 'index'])
        ->name('vouchers.index')
        ->middleware(['permission:vouchers.index']);
});
