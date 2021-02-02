<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('guests', 'Api\GuestController@index')
		->name('guests.index')
		->middleware('permission:guests.index');
});
