<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;

Route::group(['middleware' => ['auth', 'verified']], function() {
	Route::get('tags/search', [TagController::class, 'search'])
		->name('tags.search')
		->middleware('permission:tags.index');

	Route::delete('tags/{id}', [TagController::class, 'destroy'])
		->name('tags.destroy')
		->middleware('permission:tags.destroy');

	Route::put('tags/{id}', [TagController::class, 'update'])
		->name('tags.update')
		->middleware('permission:tags.edit');

	Route::get('tags/{id}/edit', [TagController::class, 'edit'])
		->name('tags.edit')
		->middleware('permission:tags.edit');

	Route::post('tags', [TagController::class, 'store'])
		->name('tags.store')
		->middleware('permission:tags.create');

	Route::get('tags/{id}/hotel/{hotel}', [TagController::class, 'show'])
		->name('tags.show')
		->middleware('permission:tags.show');

	Route::get('tags', [TagController::class, 'index'])
		->name('tags.index')
		->middleware('permission:tags.index');
});