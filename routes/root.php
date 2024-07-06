<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IdentificationTypeController;

Route::group(['middleware' => ['auth', 'role:root', 'verified']], function() {
    Route::post('users/{user}/plans', [UserController::class, 'assign'])
        ->name('users.assign');
    Route::resource('users', 'UserController');

    Route::resource('identifications', IdentificationTypeController::class);
});
