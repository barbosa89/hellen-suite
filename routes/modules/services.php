<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Service report

	Route::post('services/report', 'ServiceController@exportReport')
		->name('services.report.export')
		->middleware('permission:services.index');

	Route::get('services/report', 'ServiceController@showReportForm')
		->name('services.report')
		->middleware('permission:services.index');

	Route::post('services/{id}/report', 'ServiceController@exportServiceReport')
		->name('services.service.report.export')
		->middleware('permission:services.index');

	Route::get('services/{id}/report', 'ServiceController@showServiceReportForm')
		->name('services.service.report')
		->middleware('permission:services.index');

	# CRUD

    Route::post('services/search', 'ServiceController@search')
		->name('services.search')
		->middleware('permission:services.index');

    Route::post('services/calculate/total', 'ServiceController@calculateTotal')
		->name('services.total')
		->middleware('permission:services.index');

	Route::get('services/{id}/toggle', 'ServiceController@toggle')
		->name('services.toggle')
		->middleware('permission:services.edit');

	Route::delete('services/{id}', 'ServiceController@destroy')
		->name('services.destroy')
		->middleware('permission:services.destroy');

	Route::put('services/{id}', 'ServiceController@update')
		->name('services.update')
		->middleware('permission:services.edit');

	Route::get('services/{id}/edit', 'ServiceController@edit')
		->name('services.edit')
		->middleware('permission:services.edit');

	Route::post('services', 'ServiceController@store')
		->name('services.store')
		->middleware('permission:services.create');

	Route::get('services/create', 'ServiceController@create')
		->name('services.create')
		->middleware('permission:services.create');

	Route::get('services/{id}', 'ServiceController@show')
		->name('services.show')
		->middleware('permission:services.show');

	Route::get('services', 'ServiceController@index')
		->name('services.index')
		->middleware('permission:services.index');
});