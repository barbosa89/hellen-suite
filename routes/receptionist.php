<?php

Route::group(['middleware' => ['auth', 'role:receptionist', 'verified']], function() {
    Route::resource('guests', 'GuestController');

    Route::resource('companies', 'CompanyController');

    Route::post('products/calculate/total', 'ProductController@total')
        ->name('products.total');

    Route::post('services/calculate/total', 'ServiceController@total')
        ->name('services.total');
});
