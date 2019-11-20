<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::post('invoices/{id}/vehicles', 'VehicleController@storeForInvoice')
        ->name('invoices.vehicles.store');

    Route::get('invoices/{id}/vehicles/create', 'VehicleController@createForInvoice')
		->name('invoices.vehicles.create');

	Route::get('vehicles/export', 'VehicleController@export')
		->name('vehicles.export')
		->middleware(['permission:vehicles.index']);

    Route::get('vehicles/search', 'VehicleController@search')
		->name('vehicles.search')
		->middleware('permission:vehicles.index');

	Route::delete('vehicles/{id}', 'VehicleController@destroy')
		->name('vehicles.destroy')
		->middleware('permission:vehicles.destroy');

	Route::put('vehicles/{id}', 'VehicleController@update')
		->name('vehicles.update')
		->middleware('permission:vehicles.edit');

	Route::get('vehicles/{id}/edit', 'VehicleController@edit')
		->name('vehicles.edit')
		->middleware('permission:vehicles.edit');

	Route::post('vehicles', 'VehicleController@store')
		->name('vehicles.store')
		->middleware('permission:vehicles.create');

	Route::get('vehicles/create', 'VehicleController@create')
		->name('vehicles.create')
		->middleware('permission:vehicles.create');

	Route::get('vehicles/{id}', 'VehicleController@show')
		->name('vehicles.show')
		->middleware('permission:vehicles.show');

	Route::get('vehicles', 'VehicleController@index')
		->name('vehicles.index')
		->middleware('permission:vehicles.index');
});