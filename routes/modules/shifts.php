<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Route::post('shifts/report', 'ShiftController@report')
	// 	->name('shifts.report.export')
	// 	->middleware('permission:shifts.index');

	// Route::get('shifts/report', 'ShiftController@showReportForm')
	// 	->name('shifts.report')
	// 	->middleware('permission:shifts.index');

	// Route::post('shifts/{id}/report', 'ShiftController@propReport')
	// 	->name('shifts.prop.report.export')
	// 	->middleware('permission:shifts.index');

	// Route::get('shifts/{id}/report', 'ShiftController@showPropReportForm')
	// 	->name('shifts.prop.report')
	// 	->middleware('permission:shifts.index');

	// Route::get('shifts/{id}/transactions/{transaction}', 'ShiftController@destroyTransaction')
	// 	->name('shifts.transactions.destroy')
	// 	->middleware('permission:shifts.edit');

	// Route::post('shifts/transactions', 'ShiftController@transactions')
	// 	->name('shifts.transactions')
	// 	->middleware('permission:shifts.edit');

	// Route::get('shifts/transactions', 'ShiftController@showTransactionsForm')
	// 	->name('shifts.transactions.form')
	// 	->middleware('permission:shifts.edit');

	// Route::post('shifts/search', 'ShiftController@search')
	// 	->name('shifts.search')
	// 	->middleware('permission:shifts.index');

	Route::delete('shifts/{id}', 'ShiftController@destroy')
		->name('shifts.destroy')
		->middleware('permission:shifts.destroy');

	Route::put('shifts/{id}', 'ShiftController@update')
		->name('shifts.update')
		->middleware('permission:shifts.edit');

	Route::get('shifts/{id}/edit', 'ShiftController@edit')
		->name('shifts.edit')
		->middleware('permission:shifts.edit');

	Route::post('shifts', 'ShiftController@store')
		->name('shifts.store')
		->middleware('permission:shifts.create');

	Route::get('shifts/create', 'ShiftController@create')
		->name('shifts.create')
		->middleware('permission:shifts.create');

	Route::get('shifts/{id}', 'ShiftController@show')
		->name('shifts.show')
		->middleware('permission:shifts.show');

	Route::get('shifts', 'ShiftController@index')
		->name('shifts.index')
		->middleware('permission:shifts.index');
});
