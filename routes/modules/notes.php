<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('notes/export', 'NoteController@export')
		->name('notes.export');

	Route::get('notes/search', 'NoteController@search')
		->name('notes.search');

	Route::post('notes', 'NoteController@store')
		->name('notes.store');

	Route::get('notes/create', 'NoteController@create')
		->name('notes.create');

	Route::get('notes/{id}', 'NoteController@show')
		->name('notes.show');

	Route::get('notes', 'NoteController@index')
		->name('notes.index');
});