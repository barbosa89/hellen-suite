<?php

Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::resource('rooms', 'RoomController');

    Route::post('products/{id}/increase', 'ProductController@increase')
        ->name('products.increase');
    Route::get('products/{id}/increase', 'ProductController@showIncreaseForm')
        ->name('products.increase.form');
    Route::resource('products', 'ProductController');
    
    Route::resource('receptionists', 'ReceptionistController');
});