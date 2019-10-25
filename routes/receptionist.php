<?php

Route::group(['middleware' => ['auth', 'role:receptionist|manager', 'verified']], function() {
    Route::get('invoices/{id}/companies/create', 'InvoiceController@createCompanies')
        ->name('invoices.companies.create');

    Route::post('invoices/{id}/companies', 'InvoiceController@storeCompanies')
        ->name('invoices.companies.store');

    Route::resource('companies', 'CompanyController');

    Route::post('products/calculate/total', 'ProductController@total')
        ->name('products.total');

    Route::post('services/calculate/total', 'ServiceController@total')
        ->name('services.total');
});
