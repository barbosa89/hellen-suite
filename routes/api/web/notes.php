<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('hotels/{hotel}/notes', 'Api\NoteController@index')
		->name('notes.index')
		->middleware('permission:notes.index');
});
