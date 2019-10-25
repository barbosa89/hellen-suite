<?php

Route::group(['middleware' => ['auth', 'role:manager', 'verified']], function() {
    Route::get('assets/{id}/assign', 'AssetController@assign')
        ->name('assets.assign');

    Route::resource('assets', 'AssetController');

    Route::resource('receptionists', 'ReceptionistController');
});