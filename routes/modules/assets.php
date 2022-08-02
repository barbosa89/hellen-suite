<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function() {
    Route::post('rooms/{room}/assets/assign', 'AssetController@assign')
        ->name('assets.assign')
        ->middleware('permission:assets.edit');

    Route::get('rooms/{room}/assets/assign', 'AssetController@assignment')
        ->name('assets.assignment')
        ->middleware('permission:assets.edit');

	Route::post('assets/export', 'AssetController@export')
		->name('assets.export')
		->middleware('permission:assets.index');

	Route::get('assets/export', 'AssetController@showExportForm')
		->name('assets.export.form')
		->middleware('permission:assets.index');

    Route::post('assets/search', 'AssetController@search')
		->name('assets.search')
        ->middleware('permission:assets.index');

	# Basic resource routes

	Route::delete('assets/{id}', 'AssetController@destroy')
		->name('assets.destroy')
		->middleware('permission:assets.destroy');

	Route::put('assets/{id}', 'AssetController@update')
		->name('assets.update')
		->middleware('permission:assets.edit');

	Route::get('assets/{id}/edit', 'AssetController@edit')
		->name('assets.edit')
		->middleware('permission:assets.edit');

	Route::post('assets', 'AssetController@store')
		->name('assets.store')
		->middleware('permission:assets.create');

	Route::get('assets/create', 'AssetController@create')
		->name('assets.create')
		->middleware('permission:assets.create');

	Route::get('assets/{id}', 'AssetController@show')
		->name('assets.show')
		->middleware('permission:assets.show');

	Route::get('assets', 'AssetController@index')
		->name('assets.index')
		->middleware('permission:assets.index');
});
