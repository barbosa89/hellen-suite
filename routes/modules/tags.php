<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('tags/search', 'TagController@search')
		->name('tags.search')
		->middleware('permission:tags.index');

	Route::delete('tags/{id}', 'TagController@destroy')
		->name('tags.destroy')
		->middleware('permission:tags.destroy');

	Route::put('tags/{id}', 'TagController@update')
		->name('tags.update')
		->middleware('permission:tags.edit');

	Route::get('tags/{id}/edit', 'TagController@edit')
		->name('tags.edit')
		->middleware('permission:tags.edit');

	Route::post('tags', 'TagController@store')
		->name('tags.store')
		->middleware('permission:tags.create');

	Route::get('tags/{id}/hotel/{hotel}', 'TagController@show')
		->name('tags.show')
		->middleware('permission:tags.show');

	Route::get('tags', 'TagController@index')
		->name('tags.index')
		->middleware('permission:tags.index');
});