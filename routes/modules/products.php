<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('products/{id}/toggle', 'ProductController@toggle')
		->name('products.toggle');

    Route::post('products/{id}/increase', 'ProductController@increase')
        ->name('products.increase');

    Route::get('products/{id}/increase', 'ProductController@showIncreaseForm')
        ->name('products.increase.form');

	Route::delete('products/{id}', 'ProductController@destroy')
		->name('products.destroy')
		->middleware('permission:products.destroy');

	Route::put('products/{id}', 'ProductController@update')
		->name('products.update')
		->middleware('permission:products.edit');

	Route::get('products/{id}/edit', 'ProductController@edit')
		->name('products.edit')
		->middleware('permission:products.edit');

	Route::post('products', 'ProductController@store')
		->name('products.store')
		->middleware('permission:products.create');

	Route::get('products/create', 'ProductController@create')
		->name('products.create')
		->middleware('permission:products.create');

	Route::get('products/{id}', 'ProductController@show')
		->name('products.show')
		->middleware('permission:products.show');

	Route::get('products', 'ProductController@index')
		->name('products.index')
		->middleware('permission:products.index');
});