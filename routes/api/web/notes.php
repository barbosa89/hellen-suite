<?php

use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function () {
    Route::get('hotels/{hotel}/notes', [NoteController::class, 'index'])
        ->name('notes.index')
        ->middleware('permission:notes.index');
});
