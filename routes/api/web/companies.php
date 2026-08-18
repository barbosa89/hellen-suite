<?php

use App\Http\Controllers\Api\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v1/web', 'as' => 'api.web.', 'middleware' => ['auth', 'verified']], function (): void {
    Route::get('companies', [CompanyController::class, 'index'])
        ->name('companies.index')
        ->middleware('permission:companies.index');
});
