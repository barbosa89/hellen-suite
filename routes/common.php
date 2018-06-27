<?php

Route::get('account/{email}/activate/{token}', 'AccountController@activate')
    ->name('accounts.activate');

Route::get('account/activation', 'AccountController@showFormActivation')
    ->name('accounts.activation.form');

Route::post('account/activation', 'AccountController@activation')
    ->name('accounts.activation');