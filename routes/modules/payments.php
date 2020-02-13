<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::delete('vouchers/{voucher}/payments/{id}', 'PaymentController@destroy')
		->name('payments.destroy')
		->middleware('permission:payments.destroy');

	Route::put('vouchers/{voucher}/payments/{id}', 'PaymentController@update')
		->name('payments.update')
		->middleware('permission:payments.edit');

	Route::get('vouchers/{voucher}/payments/{id}/edit', 'PaymentController@edit')
		->name('payments.edit')
		->middleware('permission:payments.edit');

	Route::post('payments/{voucher}', 'PaymentController@store')
		->name('payments.store')
		->middleware('permission:payments.create');

	Route::get('vouchers/{voucher}/payments/create', 'PaymentController@create')
		->name('payments.create')
		->middleware('permission:payments.create');

	Route::get('vouchers/{voucher}/payments', 'PaymentController@index')
		->name('payments.index')
		->middleware('permission:payments.index');
});