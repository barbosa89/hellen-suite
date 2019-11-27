<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::delete('invoices/{invoice}/payments/{id}', 'PaymentController@destroy')
		->name('payments.destroy')
		->middleware('permission:payments.destroy');

	Route::put('invoices/{invoice}/payments/{id}', 'PaymentController@update')
		->name('payments.update')
		->middleware('permission:payments.edit');

	Route::get('invoices/{invoice}/payments/{id}/edit', 'PaymentController@edit')
		->name('payments.edit')
		->middleware('permission:payments.edit');

	Route::post('payments/{invoice}', 'PaymentController@store')
		->name('payments.store')
		->middleware('permission:payments.create');

	Route::get('invoices/{invoice}/payments/create', 'PaymentController@create')
		->name('payments.create')
		->middleware('permission:payments.create');

	Route::get('invoices/{invoice}/payments', 'PaymentController@index')
		->name('payments.index')
		->middleware('permission:payments.index');
});