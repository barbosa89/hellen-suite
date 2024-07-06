<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::post('rooms/{room}/assets/assign', [AssetController::class, 'assign'])
        ->name('assets.assign')
        ->middleware('permission:assets.edit');

    Route::get('rooms/{room}/assets/assign', [AssetController::class, 'assignment'])
        ->name('assets.assignment')
        ->middleware('permission:assets.edit');

	Route::post('assets/export', [AssetController::class, 'export'])
		->name('assets.export')
		->middleware('permission:assets.index');

	Route::get('assets/export', [AssetController::class, 'showExportForm'])
		->name('assets.export.form')
		->middleware('permission:assets.index');

    Route::post('assets/search', [AssetController::class, 'search'])
		->name('assets.search')
        ->middleware('permission:assets.index');

	# Basic resource routes

	Route::delete('assets/{id}', [AssetController::class, 'destroy'])
		->name('assets.destroy')
		->middleware('permission:assets.destroy');

	Route::put('assets/{id}', [AssetController::class, 'update'])
		->name('assets.update')
		->middleware('permission:assets.edit');

	Route::get('assets/{id}/edit', [AssetController::class, 'edit'])
		->name('assets.edit')
		->middleware('permission:assets.edit');

	Route::post('assets', [AssetController::class, 'store'])
		->name('assets.store')
		->middleware('permission:assets.create');

	Route::get('assets/create', [AssetController::class, 'create'])
		->name('assets.create')
		->middleware('permission:assets.create');

	Route::get('assets/{id}', [AssetController::class, 'show'])
		->name('assets.show')
		->middleware('permission:assets.show');

	Route::get('assets', [AssetController::class, 'index'])
		->name('assets.index')
		->middleware('permission:assets.index');
});
