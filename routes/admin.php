<?php

Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::get('assets/{id}/assign', 'AssetController@assign')
        ->name('assets.assign');

    Route::resource('assets', 'AssetController');

    Route::post('products/{id}/increase', 'ProductController@increase')
        ->name('products.increase');

    Route::get('products/{id}/increase', 'ProductController@showIncreaseForm')
        ->name('products.increase.form');

    Route::resource('products', 'ProductController');

    Route::resource('receptionists', 'ReceptionistController');

    Route::resource('services', 'ServiceController');

    Route::resource('rooms', 'RoomController');
});