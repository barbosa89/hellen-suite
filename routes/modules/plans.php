<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::get('plans/renew', [PlanController::class, 'renew'])
        ->name('plans.renew')
        ->middleware('role:manager');

    Route::get('plans/{id}/buy', [PlanController::class, 'buy'])
        ->name('plans.buy')
        ->middleware('role:manager');

    Route::get('plans/choose', [PlanController::class, 'choose'])
        ->name('plans.choose')
        ->middleware('role:manager');

	Route::put('plans/{id}', [PlanController::class, 'update'])
        ->name('plans.update')
        ->middleware('role:root');

	Route::get('plans/{id}/edit', [PlanController::class, 'edit'])
        ->name('plans.edit')
        ->middleware('role:root');

	Route::get('plans', [PlanController::class, 'index'])
        ->name('plans.index')
        ->middleware('role:root');
});
