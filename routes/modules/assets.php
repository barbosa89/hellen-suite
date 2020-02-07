<?php

Route::group(['middleware' => ['auth', 'verified']], function() {
	# Asset transactions

	Route::get('assets/{id}/transactions', 'AssetTransactionController@index')
		->name('assets.transactions.index')
		->middleware('permission:assets.edit');

	# Basic resource routes

	Route::delete('assets/{id}/maintenance/{maintenance}', 'AssetController@destroyMaintenance')
		->name('assets.maintenance.destroy')
		->middleware('permission:assets.edit');

	Route::post('assets/{id}/maintenance/{maintenance}/edit', 'AssetController@updateMaintenance')
		->name('assets.maintenance.update')
		->middleware('permission:assets.edit');

	Route::get('assets/{id}/maintenance/{maintenance}/edit', 'AssetController@showMaintenanceEditForm')
		->name('assets.maintenance.edit')
		->middleware('permission:assets.edit');

	Route::post('assets/{id}/maintenance', 'AssetController@maintenance')
		->name('assets.maintenance')
		->middleware('permission:assets.edit');

	Route::get('assets/{id}/maintenance', 'AssetController@showMaintenanceForm')
		->name('assets.maintenance.form')
		->middleware('permission:assets.edit');

	Route::post('assets/report', 'AssetController@report')
		->name('assets.report.export')
		->middleware('permission:assets.index');

	Route::get('assets/report', 'AssetController@showReportForm')
		->name('assets.report')
		->middleware('permission:assets.index');

    Route::post('assets/search', 'AssetController@search')
		->name('assets.search')
		->middleware('permission:assets.index');

	Route::get('assets/{id}/assign', 'AssetController@assign')
		->name('assets.assign')
		->middleware('permission:assets.edit');

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