<?php

use App\Http\Controllers\Api\GuestController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function (): void {
    Route::get('guests', [GuestController::class, 'index'])
        ->name('guests.index')
        ->middleware('permission:guests.index');
});
