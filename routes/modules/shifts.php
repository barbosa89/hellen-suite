<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('shifts/{id}', 'ShiftController@show')
		->name('shifts.show')
		->middleware('permission:shifts.show');

	Route::get('shifts', 'ShiftController@index')
		->name('shifts.index')
		->middleware('permission:shifts.index');
});
