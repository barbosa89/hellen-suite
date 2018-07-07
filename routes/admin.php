<?php

Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::resource('rooms', 'RoomController');
    Route::resource('products', 'ProductController');
    Route::resource('receptionists', 'ReceptionistController');
});