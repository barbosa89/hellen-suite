<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('shifts/{id}/close', [ShiftController::class, 'close'])
		->name('shifts.close')
		->middleware('permission:shifts.close');

	Route::get('shifts/{id}/export', [ShiftController::class, 'export'])
		->name('shifts.export')
		->middleware('permission:shifts.show');

	Route::get('shifts/{id}', [ShiftController::class, 'show'])
		->name('shifts.show')
		->middleware('permission:shifts.show');

	Route::get('shifts', [ShiftController::class, 'index'])
		->name('shifts.index')
		->middleware('permission:shifts.index');
});
