<?php

use App\Http\Controllers\AssetMaintenanceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::delete('assets/{asset}/maintenances/{maintenance}', [AssetMaintenanceController::class, 'destroy'])
		->name('assets.maintenances.destroy')
		->middleware('permission:assets.edit');

	Route::patch('assets/{asset}/maintenances/{maintenance}', [AssetMaintenanceController::class, 'update'])
		->name('assets.maintenances.update')
		->middleware('permission:assets.edit');

	Route::get('assets/{asset}/maintenances/{maintenance}/edit', [AssetMaintenanceController::class, 'edit'])
		->name('assets.maintenances.edit')
		->middleware('permission:assets.edit');

	Route::post('assets/{asset}/maintenances', [AssetMaintenanceController::class, 'store'])
		->name('assets.maintenances.store')
		->middleware('permission:assets.edit');

	Route::get('assets/{asset}/maintenances/create', [AssetMaintenanceController::class, 'create'])
		->name('assets.maintenances.create')
		->middleware('permission:assets.edit');
});
