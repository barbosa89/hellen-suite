<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('companies/export', [CompanyController::class, 'export'])
        ->name('companies.export')
        ->middleware(['permission:companies.index']);

    Route::get('companies/search', [CompanyController::class, 'search'])
        ->name('companies.search')
        ->middleware(['permission:companies.index']);

    Route::get('vouchers/{id}/companies/create', [CompanyController::class, 'createForvoucher'])
        ->name('vouchers.companies.create')
        ->middleware('permission:companies.create', 'open_shift');

    Route::post('vouchers/{id}/companies', [CompanyController::class, 'storeForvoucher'])
        ->name('vouchers.companies.store')
        ->middleware('permission:companies.create', 'open_shift');

    Route::delete('companies/{id}', [CompanyController::class, 'destroy'])
        ->name('companies.destroy')
        ->middleware('permission:companies.destroy');

    Route::put('companies/{id}', [CompanyController::class, 'update'])
        ->name('companies.update')
        ->middleware('permission:companies.edit');

    Route::get('companies/{id}/edit', [CompanyController::class, 'edit'])
        ->name('companies.edit')
        ->middleware('permission:companies.edit');

    Route::post('companies', [CompanyController::class, 'store'])
        ->name('companies.store')
        ->middleware('permission:companies.create');

    Route::get('companies/create', [CompanyController::class, 'create'])
        ->name('companies.create')
        ->middleware('permission:companies.create');

    Route::get('companies/{id}', [CompanyController::class, 'show'])
        ->name('companies.show')
        ->middleware('permission:companies.show');

    Route::get('companies', [CompanyController::class, 'index'])
        ->name('companies.index')
        ->middleware('permission:companies.index');
});
