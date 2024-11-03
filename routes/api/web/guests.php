<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuestController;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('guests', [GuestController::class, 'index'])
		->name('guests.index')
		->middleware('permission:guests.index');
});
