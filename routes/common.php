<?php

Route::get('guests/search', 'GuestController@search')
    ->name('guests.search')
    ->middleware(['auth', 'role:manager|admin|receptionist', 'verified']);

Route::get('companies/search', 'CompanyController@search')
    ->name('companies.search')
    ->middleware(['auth', 'role:manager|admin|receptionist', 'verified']);