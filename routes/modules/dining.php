<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Service report

	Route::post('dining/report', 'DiningServiceController@exportReport')
		->name('dining.report.export')
		->middleware('permission:dining.index');

	Route::get('dining/report', 'DiningServiceController@showReportForm')
		->name('dining.report')
		->middleware('permission:dining.index');

	Route::post('dining/{id}/report', 'DiningServiceController@exportServiceReport')
		->name('dining.service.report.export')
		->middleware('permission:dining.index');

	Route::get('dining/{id}/report', 'DiningServiceController@showServiceReportForm')
		->name('dining.service.report')
		->middleware('permission:dining.index');

	# CRUD

    Route::post('dining/search', 'DiningServiceController@search')
		->name('dining.search')
		->middleware('permission:dining.index');

	Route::delete('dining/{id}', 'DiningServiceController@destroy')
		->name('dining.destroy')
		->middleware('permission:dining.destroy');

	Route::put('dining/{id}', 'DiningServiceController@update')
		->name('dining.update')
		->middleware('permission:dining.edit');

	Route::get('dining/{id}/edit', 'DiningServiceController@edit')
		->name('dining.edit')
		->middleware('permission:dining.edit');

	Route::post('dining', 'DiningServiceController@store')
		->name('dining.store')
		->middleware('permission:dining.create');

	Route::get('dining/create', 'DiningServiceController@create')
		->name('dining.create')
		->middleware('permission:dining.create');

	Route::get('dining/{id}', 'DiningServiceController@show')
		->name('dining.show')
		->middleware('permission:dining.show');

	Route::get('dining', 'DiningServiceController@index')
		->name('dining.index')
		->middleware('permission:dining.index');
});