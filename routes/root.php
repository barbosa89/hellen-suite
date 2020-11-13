<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'role:root', 'verified']], function() {
    Route::post('users/{user}/plans', 'UserController@assign')
        ->name('users.assign');
    Route::resource('users', 'UserController');

    Route::resource('identifications', 'IdentificationTypeController');

    // Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
