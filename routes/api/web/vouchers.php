<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('hotels/{hotel}/vouchers/datasets/guests/{period}', 'Api\VoucherController@getGuestDataset')
		->name('vouchers.datasets.guests')
		->middleware('permission:vouchers.index');

    Route::get('hotels/{hotel}/vouchers', 'Api\VoucherController@index')
        ->name('vouchers.index')
        ->middleware(['permission:vouchers.index']);
});
