<?php

use App\Http\Controllers\IdentificationTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'role:root', 'verified']], function (): void {
    Route::post('users/{user}/plans', [UserController::class, 'assign'])
        ->name('users.assign');
    Route::resource('users', UserController::class);

    Route::resource('identifications', IdentificationTypeController::class);
});
