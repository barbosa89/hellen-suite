<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;

Route::group(['middleware' => ['auth', 'role:manager', 'verified']], function() {
	Route::post('team/members/{id}/permissions', [TeamController::class, 'storePermissions'])
		->name('team.permissions.store');

	Route::get('team/members/{id}/permissions', [TeamController::class, 'permissions'])
		->name('team.permissions');

	Route::get('team/members/search', [TeamController::class, 'search'])
		->name('team.search');

	Route::delete('team/members/{id}', [TeamController::class, 'destroy'])
		->name('team.destroy');

	Route::put('team/members/{id}', [TeamController::class, 'update'])
		->name('team.update');

	Route::post('team/members/{id}/attach', [TeamController::class, 'attach'])
		->name('team.assign.attach');

	Route::get('team/members/{id}/assign', [TeamController::class, 'assign'])
		->name('team.assign');

	Route::get('team/members/{id}/edit', [TeamController::class, 'edit'])
		->name('team.edit');

	Route::post('team/members', [TeamController::class, 'store'])
		->name('team.store');

	Route::get('team/members/create', [TeamController::class, 'create'])
		->name('team.create');

	Route::get('team/members/{id}', [TeamController::class, 'show'])
		->name('team.show');

	Route::get('team/members', [TeamController::class, 'index'])
		->name('team.index');
});