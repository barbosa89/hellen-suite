<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('companies/search', 'CompanyController@search')
		->name('companies.search')
		->middleware(['permission:companies.index']);

    Route::get('invoices/{id}/companies/create', 'CompanyController@createForInvoice')
        ->name('invoices.companies.create')
        ->middleware('permission:companies.create');

    Route::post('invoices/{id}/companies', 'CompanyController@storeForInvoice')
        ->name('invoices.companies.store')
        ->middleware('permission:companies.create');

	Route::delete('companies/{id}', 'CompanyController@destroy')
		->name('companies.destroy')
		->middleware('permission:companies.destroy');

	Route::put('companies/{id}', 'CompanyController@update')
		->name('companies.update')
		->middleware('permission:companies.edit');

	Route::get('companies/{id}/edit', 'CompanyController@edit')
		->name('companies.edit')
		->middleware('permission:companies.edit');

	Route::post('companies', 'CompanyController@store')
		->name('companies.store')
		->middleware('permission:companies.create');

	Route::get('companies/create', 'CompanyController@create')
		->name('companies.create')
		->middleware('permission:companies.create');

	Route::get('companies/{id}', 'CompanyController@show')
		->name('companies.show')
		->middleware('permission:companies.show');

	Route::get('companies', 'CompanyController@index')
		->name('companies.index')
		->middleware('permission:companies.index');


});