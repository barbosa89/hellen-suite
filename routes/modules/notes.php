<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('notes/export', 'NoteController@export')
		->name('notes.export')
		->middleware('permission:notes.index');

	Route::get('notes/search', 'NoteController@search')
		->name('notes.search')
		->middleware('permission:notes.index');

	Route::post('notes', 'NoteController@store')
		->name('notes.store')
		->middleware('permission:notes.create');

	Route::get('notes/create', 'NoteController@create')
		->name('notes.create')
		->middleware('permission:notes.create');

	Route::get('notes/{id}', 'NoteController@show')
		->name('notes.show')
		->middleware('permission:notes.show');

	Route::get('notes', 'NoteController@index')
		->name('notes.index')
		->middleware('permission:notes.index');
});