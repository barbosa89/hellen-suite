<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::delete('products/{id}/sales/{sale}', 'SaleController@destroy')
        ->name('sales.destroy')
        ->middleware(['permission:sales.destroy']);

    Route::put('products/{id}/sales/{sale}', 'SaleController@update')
        ->name('sales.update')
        ->middleware(['permission:sales.edit']);

	Route::get('products/{id}/sales/{sale}/edit', 'SaleController@edit')
        ->name('sales.edit')
        ->middleware(['permission:sales.edit']);

	Route::post('products/{id}/sales', 'SaleController@store')
        ->name('sales.store')
        ->middleware(['permission:sales.create']);

	Route::get('products/{id}/sales/create', 'SaleController@create')
        ->name('sales.create')
        ->middleware(['permission:sales.create']);

	Route::get('products/{id}/sales', 'SaleController@index')
        ->name('sales.index')
        ->middleware(['permission:sales.index']);
});
