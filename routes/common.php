<?php

Route::post('guests/search/unregistered', 'GuestController@searchUnregistered')
    ->name('guests.search.unregistered')
    ->middleware(['auth', 'permission:guests.index', 'verified']);

Route::get('guests/search', 'GuestController@search')
    ->name('guests.search')
    ->middleware(['auth', 'permission:guests.index', 'verified']);

Route::get('companies/search', 'CompanyController@search')
    ->name('companies.search')
    ->middleware(['auth', 'permission:companies.index', 'verified']);