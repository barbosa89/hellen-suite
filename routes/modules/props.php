<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('props/{id}/transactions/{transaction}', 'PropController@destroyTransaction')
		->name('props.transactions.destroy')
		->middleware('permission:props.edit');

	Route::post('props/transactions', 'PropController@transactions')
		->name('props.transactions')
		->middleware('permission:props.edit');

	Route::get('props/transactions', 'PropController@showTransactionsForm')
		->name('props.transactions.form')
		->middleware('permission:props.edit');

	Route::post('props/search', 'PropController@search')
		->name('props.search')
		->middleware('permission:props.index');

	Route::delete('props/{id}', 'PropController@destroy')
		->name('props.destroy')
		->middleware('permission:props.destroy');

	Route::put('props/{id}', 'PropController@update')
		->name('props.update')
		->middleware('permission:props.edit');

	Route::get('props/{id}/edit', 'PropController@edit')
		->name('props.edit')
		->middleware('permission:props.edit');

	Route::post('props', 'PropController@store')
		->name('props.store')
		->middleware('permission:props.create');

	Route::get('props/create', 'PropController@create')
		->name('props.create')
		->middleware('permission:props.create');

	Route::get('props/{id}', 'PropController@show')
		->name('props.show')
		->middleware('permission:props.show');

	Route::get('props', 'PropController@index')
		->name('props.index')
		->middleware('permission:props.index');
});