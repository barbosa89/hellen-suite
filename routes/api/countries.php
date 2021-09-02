<?php

use Illuminate\Support\Facades\Route;

Route::get('countries', 'Api\CountryController@index')
    ->name('countries.index');
