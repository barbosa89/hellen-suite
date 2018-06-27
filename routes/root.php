<?php

Route::group(['middleware' => ['auth', 'role:root']], function() {
    Route::resource('users', 'UserController');

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});
