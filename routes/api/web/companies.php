<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyController;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function() {
	Route::get('companies', [CompanyController::class, 'index'])
		->name('companies.index')
		->middleware('permission:companies.index');
});
