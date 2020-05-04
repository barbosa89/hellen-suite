<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('shifts/{id}/close', 'ShiftController@close')
		->name('shifts.close')
		->middleware('permission:shifts.close');

	Route::get('shifts/{id}/export', 'ShiftController@export')
		->name('shifts.export')
		->middleware('permission:shifts.show');

	Route::get('shifts/{id}', 'ShiftController@show')
		->name('shifts.show')
		->middleware('permission:shifts.show');

	Route::get('shifts', 'ShiftController@index')
		->name('shifts.index')
		->middleware('permission:shifts.index');
});
