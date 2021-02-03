<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('companies', 'Api\CompanyController@index')
		->name('companies.index')
		->middleware('permission:companies.index');
});
