<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::post('hotels/assigned', [HotelController::class, 'getAssigned'])
        ->name('hotels.assigned')
        ->middleware(['permission:hotels.index']);

    Route::post('hotels/different', [HotelController::class, 'getDifferentTo'])
        ->name('hotels.different')
        ->middleware(['permission:hotels.index']);

    Route::get('hotels/search', [HotelController::class, 'search'])
        ->name('hotels.search')
        ->middleware(['permission:hotels.index']);

	Route::delete('hotels/{id}', [HotelController::class, 'destroy'])
        ->name('hotels.destroy')
        ->middleware(['permission:hotels.destroy']);

	Route::get('hotels/{id}/toggle', [HotelController::class, 'toggle'])
        ->name('hotels.toggle')
        ->middleware(['permission:hotels.edit']);

    Route::put('hotels/{id}', [HotelController::class, 'update'])
        ->name('hotels.update')
        ->middleware(['permission:hotels.edit']);

	Route::get('hotels/{id}/edit', [HotelController::class, 'edit'])
        ->name('hotels.edit')
        ->middleware(['permission:hotels.edit']);

	Route::post('hotels', [HotelController::class, 'store'])
        ->name('hotels.store')
        ->middleware(['verify_plan', 'permission:hotels.create']);

	Route::get('hotels/create', [HotelController::class, 'create'])
        ->name('hotels.create')
        ->middleware(['verify_plan', 'permission:hotels.create']);

	Route::get('hotels/{id}', [HotelController::class, 'show'])
        ->name('hotels.show')
        ->middleware(['permission:hotels.show']);

	Route::get('hotels', [HotelController::class, 'index'])
        ->name('hotels.index')
        ->middleware(['permission:hotels.index']);
});
