<?php

Route::group(['middleware' => ['auth', 'role:manager', 'verified']], function() {
	Route::post('team/members/{id}/permissions', 'TeamController@storePermissions')
		->name('team.permissions.store');

	Route::get('team/members/{id}/permissions', 'TeamController@permissions')
		->name('team.permissions');

	Route::get('team/members/search', 'TeamController@search')
		->name('team.search');

	Route::delete('team/members/{id}', 'TeamController@destroy')
		->name('team.destroy');

	Route::put('team/members/{id}', 'TeamController@update')
		->name('team.update');

	Route::post('team/members/{id}/attach', 'TeamController@attach')
		->name('team.assign.attach');

	Route::get('team/members/{id}/assign', 'TeamController@assign')
		->name('team.assign');

	Route::get('team/members/{id}/edit', 'TeamController@edit')
		->name('team.edit');

	Route::post('team/members', 'TeamController@store')
		->name('team.store');

	Route::get('team/members/create', 'TeamController@create')
		->name('team.create');

	Route::get('team/members/{id}', 'TeamController@show')
		->name('team.show');

	Route::get('team/members', 'TeamController@index')
		->name('team.index');
});