<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::post('notes', 'NoteController@store')
		->name('notes.store');

	Route::get('notes/{id}', 'NoteController@show')
		->name('notes.show');

	Route::get('notes', 'NoteController@index')
		->name('notes.index');
});