<?php

Route::group(['middleware' => ['auth', 'role:root', 'verified']], function() {
    Route::resource('users', 'UserController');

    Route::resource('identifications', 'IdentificationTypeController');

    \Aschmelyun\Larametrics\Larametrics::routes();

    // TODO: Crear listado de vehículos para los manager y otros del hotel
    // Route::resource('vehicles', 'VehicleTypeController');

    // Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
