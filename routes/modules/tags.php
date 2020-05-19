<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('tags/search', 'TagController@search')
		->name('tags.search');

	Route::post('tags', 'TagController@store')
		->name('tags.store');

	Route::get('tags/{id}/hotel/{hotel}', 'TagController@show')
		->name('tags.show');

	Route::get('tags', 'TagController@index')
		->name('tags.index');
});