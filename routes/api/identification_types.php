<?php

use Illuminate\Support\Facades\Route;

Route::get('identification-types', 'Api\IdentificationTypeController@index')
    ->name('identification_types.index');
