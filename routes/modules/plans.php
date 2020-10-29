<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('plans/renew', 'PlanController@renew')
        ->name('plans.renew')
        ->middleware('role:manager');

    Route::get('plans/choose', 'PlanController@choose')
        ->name('plans.choose')
        ->middleware('role:manager');

	Route::put('plans/{id}', 'PlanController@update')
        ->name('plans.update')
        ->middleware('role:root');

	Route::get('plans/{id}/edit', 'PlanController@edit')
        ->name('plans.edit')
        ->middleware('role:root');

	Route::get('plans', 'PlanController@index')
        ->name('plans.index')
        ->middleware('role:root');
});
