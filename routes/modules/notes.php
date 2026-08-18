<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function (): void {
    Route::get('notes/export', [NoteController::class, 'export'])
        ->name('notes.export')
        ->middleware('permission:notes.index');

    Route::get('notes/search', [NoteController::class, 'search'])
        ->name('notes.search')
        ->middleware('permission:notes.index');

    Route::post('notes', [NoteController::class, 'store'])
        ->name('notes.store')
        ->middleware('permission:notes.create');

    Route::get('notes/create', [NoteController::class, 'create'])
        ->name('notes.create')
        ->middleware('permission:notes.create');

    Route::get('notes/{id}', [NoteController::class, 'show'])
        ->name('notes.show')
        ->middleware('permission:notes.show');

    Route::get('notes', [NoteController::class, 'index'])
        ->name('notes.index')
        ->middleware('permission:notes.index');
});
