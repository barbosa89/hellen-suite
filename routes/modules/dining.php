<?php

use App\Http\Controllers\DiningServiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function () {
    // Service report

    Route::post('dining/report', [DiningServiceController::class, 'exportReport'])
        ->name('dining.report.export')
        ->middleware('permission:dining.index');

    Route::get('dining/report', [DiningServiceController::class, 'showReportForm'])
        ->name('dining.report')
        ->middleware('permission:dining.index');

    Route::post('dining/{id}/report', [DiningServiceController::class, 'exportServiceReport'])
        ->name('dining.service.report.export')
        ->middleware('permission:dining.index');

    Route::get('dining/{id}/report', [DiningServiceController::class, 'showServiceReportForm'])
        ->name('dining.service.report')
        ->middleware('permission:dining.index');

    // CRUD

    Route::post('dining/search', [DiningServiceController::class, 'search'])
        ->name('dining.search')
        ->middleware('permission:dining.index');

    Route::delete('dining/{id}', [DiningServiceController::class, 'destroy'])
        ->name('dining.destroy')
        ->middleware('permission:dining.destroy');

    Route::put('dining/{id}', [DiningServiceController::class, 'update'])
        ->name('dining.update')
        ->middleware('permission:dining.edit');

    Route::get('dining/{id}/edit', [DiningServiceController::class, 'edit'])
        ->name('dining.edit')
        ->middleware('permission:dining.edit');

    Route::post('dining', [DiningServiceController::class, 'store'])
        ->name('dining.store')
        ->middleware('permission:dining.create');

    Route::get('dining/create', [DiningServiceController::class, 'create'])
        ->name('dining.create')
        ->middleware('permission:dining.create');

    Route::get('dining/{id}', [DiningServiceController::class, 'show'])
        ->name('dining.show')
        ->middleware('permission:dining.show');

    Route::get('dining', [DiningServiceController::class, 'index'])
        ->name('dining.index')
        ->middleware('permission:dining.index');
});
