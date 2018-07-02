<?php

Route::group(['middleware' => ['auth', 'role:admin']], function() {
    Route::resource('receptionists', 'ReceptionistController');
});