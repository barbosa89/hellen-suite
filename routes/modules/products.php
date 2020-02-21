<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	// Products vouchers routes

	Route::get('products/{id}/vouchers/{voucher}', 'ProductVoucherController@destroy')
		->name('products.vouchers.destroy')
		->middleware('permission:products.vouchers');

	Route::post('products/vouchers', 'ProductVoucherController@store')
		->name('products.vouchers')
		->middleware('permission:products.vouchers');

	Route::get('products/vouchers', 'ProductVoucherController@create')
		->name('products.vouchers.create')
		->middleware('permission:products.vouchers');

	// Products routes

    Route::post('products/search', 'ProductController@search')
		->name('products.search')
		->middleware('permission:products.index');

	Route::post('products/calculate/total', 'ProductController@total')
		->name('products.total')
		->middleware('permission:products.index');

	Route::get('products/{id}/toggle', 'ProductController@toggle')
		->name('products.toggle')
		->middleware('permission:products.edit');

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