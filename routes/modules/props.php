<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Props report

	Route::post('props/report', 'PropController@report')
		->name('props.report.export')
		->middleware('permission:props.index');

	Route::get('props/report', 'PropController@showReportForm')
		->name('props.report')
		->middleware('permission:props.index');

	Route::post('props/{id}/report', 'PropController@propReport')
		->name('props.prop.report.export')
		->middleware('permission:props.index');

	Route::get('props/{id}/report', 'PropController@showPropReportForm')
		->name('props.prop.report')
		->middleware('permission:props.index');

	// Props transactions routes

	Route::get('props/{id}/vouchers/{voucher}', 'PropVoucherController@destroy')
		->name('props.vouchers.destroy')
		->middleware('permission:props.vouchers');

	Route::post('props/vouchers', 'PropVoucherController@store')
		->name('props.vouchers')
		->middleware('permission:props.vouchers');

	Route::get('props/vouchers', 'PropVoucherController@create')
		->name('props.vouchers.create')
		->middleware('permission:props.vouchers'); // Agregar más permisos

	// Props routes

	Route::post('props/search', 'PropController@search')
		->name('props.search')
		->middleware('permission:props.index');

	Route::delete('props/{id}', 'PropController@destroy')
		->name('props.destroy')
		->middleware('permission:props.destroy');

	Route::put('props/{id}', 'PropController@update')
		->name('props.update')
		->middleware('permission:props.edit');

	Route::get('props/{id}/edit', 'PropController@edit')
		->name('props.edit')
		->middleware('permission:props.edit');

	Route::post('props', 'PropController@store')
		->name('props.store')
		->middleware('permission:props.create');

	Route::get('props/create', 'PropController@create')
		->name('props.create')
		->middleware('permission:props.create');

	Route::get('props/{id}', 'PropController@show')
		->name('props.show')
		->middleware('permission:props.show');

	Route::get('props', 'PropController@index')
		->name('props.index')
		->middleware('permission:props.index');
});