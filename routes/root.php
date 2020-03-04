<?php

Route::group(['middleware' => ['auth', 'role:root', 'verified']], function() {
    Route::resource('users', 'UserController');

    Route::resource('identifications', 'IdentificationTypeController');

    \Aschmelyun\Larametrics\Larametrics::routes();

    // Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
