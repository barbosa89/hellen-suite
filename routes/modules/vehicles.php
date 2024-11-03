<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::post('vouchers/{id}/vehicles', [VehicleController::class, 'storeForvoucher'])
		->name('vouchers.vehicles.store')
		->middleware(['permission:vouchers.edit', 'open_shift']);

    Route::get('vouchers/{id}/vehicles/create', [VehicleController::class, 'createForvoucher'])
		->name('vouchers.vehicles.create')
		->middleware(['permission:vouchers.edit', 'open_shift']);

	Route::get('vehicles/export', [VehicleController::class, 'export'])
		->name('vehicles.export')
		->middleware(['permission:vehicles.index']);

    Route::get('vehicles/search', [VehicleController::class, 'search'])
		->name('vehicles.search')
		->middleware('permission:vehicles.index');

	Route::delete('vehicles/{id}', [VehicleController::class, 'destroy'])
		->name('vehicles.destroy')
		->middleware('permission:vehicles.destroy');

	Route::put('vehicles/{id}', [VehicleController::class, 'update'])
		->name('vehicles.update')
		->middleware('permission:vehicles.edit');

	Route::get('vehicles/{id}/edit', [VehicleController::class, 'edit'])
		->name('vehicles.edit')
		->middleware('permission:vehicles.edit');

	Route::post('vehicles', [VehicleController::class, 'store'])
		->name('vehicles.store')
		->middleware('permission:vehicles.create');

	Route::get('vehicles/create', [VehicleController::class, 'create'])
		->name('vehicles.create')
		->middleware('permission:vehicles.create');

	Route::get('vehicles', [VehicleController::class, 'index'])
		->name('vehicles.index')
		->middleware('permission:vehicles.index');
});