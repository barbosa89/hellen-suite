<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::post('hotels/assigned', 'HotelController@getAssigned')
        ->name('hotels.assigned')
        ->middleware(['permission:hotels.index']);

    Route::post('hotels/different', 'HotelController@getDifferentTo')
        ->name('hotels.different')
        ->middleware(['permission:hotels.index']);

    Route::get('hotels/search', 'HotelController@search')
        ->name('hotels.search')
        ->middleware(['permission:hotels.index']);

	Route::delete('hotels/{id}', 'HotelController@destroy')
        ->name('hotels.destroy')
        ->middleware(['permission:hotels.destroy']);

	Route::get('hotels/{id}/toggle', 'HotelController@toggle')
        ->name('hotels.toggle')
        ->middleware(['permission:hotels.edit']);

    Route::put('hotels/{id}', 'HotelController@update')
        ->name('hotels.update')
        ->middleware(['permission:hotels.edit']);

	Route::get('hotels/{id}/edit', 'HotelController@edit')
        ->name('hotels.edit')
        ->middleware(['permission:hotels.edit']);

	Route::post('hotels', 'HotelController@store')
        ->name('hotels.store')
        ->middleware(['verify_plan', 'permission:hotels.create']);

	Route::get('hotels/create', 'HotelController@create')
        ->name('hotels.create')
        ->middleware(['verify_plan', 'permission:hotels.create']);

	Route::get('hotels/{id}', 'HotelController@show')
        ->name('hotels.show')
        ->middleware(['permission:hotels.show']);

	Route::get('hotels', 'HotelController@index')
        ->name('hotels.index')
        ->middleware(['permission:hotels.index']);
});
