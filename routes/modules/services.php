<?php

use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    // Service report

    Route::post('services/report', [ServiceController::class, 'exportReport'])
        ->name('services.report.export')
        ->middleware('permission:services.index');

    Route::get('services/report', [ServiceController::class, 'showReportForm'])
        ->name('services.report')
        ->middleware('permission:services.index');

    Route::post('services/{id}/report', [ServiceController::class, 'exportServiceReport'])
        ->name('services.service.report.export')
        ->middleware('permission:services.index');

    Route::get('services/{id}/report', [ServiceController::class, 'showServiceReportForm'])
        ->name('services.service.report')
        ->middleware('permission:services.index');

    // CRUD

    Route::post('services/search', [ServiceController::class, 'search'])
        ->name('services.search')
        ->middleware('permission:services.index');

    Route::post('services/calculate/total', [ServiceController::class, 'calculateTotal'])
        ->name('services.total')
        ->middleware('permission:services.index');

    Route::get('services/{id}/toggle', [ServiceController::class, 'toggle'])
        ->name('services.toggle')
        ->middleware('permission:services.edit');

    Route::delete('services/{id}', [ServiceController::class, 'destroy'])
        ->name('services.destroy')
        ->middleware('permission:services.destroy');

    Route::put('services/{id}', [ServiceController::class, 'update'])
        ->name('services.update')
        ->middleware('permission:services.edit');

    Route::get('services/{id}/edit', [ServiceController::class, 'edit'])
        ->name('services.edit')
        ->middleware('permission:services.edit');

    Route::post('services', [ServiceController::class, 'store'])
        ->name('services.store')
        ->middleware('permission:services.create');

    Route::get('services/create', [ServiceController::class, 'create'])
        ->name('services.create')
        ->middleware('permission:services.create');

    Route::get('services/{id}', [ServiceController::class, 'show'])
        ->name('services.show')
        ->middleware('permission:services.show');

    Route::get('services', [ServiceController::class, 'index'])
        ->name('services.index')
        ->middleware('permission:services.index');
});
