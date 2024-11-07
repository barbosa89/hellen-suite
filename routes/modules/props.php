<?php

use App\Http\Controllers\PropController;
use App\Http\Controllers\PropVoucherController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function (): void {
    // Props report

    Route::post('props/report', [PropController::class, 'exportReport'])
        ->name('props.report.export')
        ->middleware('permission:props.index');

    Route::get('props/report', [PropController::class, 'showReportForm'])
        ->name('props.report')
        ->middleware('permission:props.index');

    Route::post('props/{id}/report', [PropController::class, 'exportPropReport'])
        ->name('props.prop.report.export')
        ->middleware('permission:props.index');

    Route::get('props/{id}/report', [PropController::class, 'showPropReportForm'])
        ->name('props.prop.report')
        ->middleware('permission:props.index');

    // Props transactions routes

    Route::get('props/{id}/vouchers/{voucher}', [PropVoucherController::class, 'destroy'])
        ->name('props.vouchers.destroy')
        ->middleware('permission:props.vouchers');

    Route::post('props/vouchers', [PropVoucherController::class, 'store'])
        ->name('props.vouchers')
        ->middleware('permission:props.vouchers');

    Route::get('props/vouchers', [PropVoucherController::class, 'create'])
        ->name('props.vouchers.create')
        ->middleware('permission:props.vouchers'); // Agregar más permisos

    // Props routes

    Route::get('props/search', [PropController::class, 'search'])
        ->name('props.search')
        ->middleware('permission:props.index');

    Route::delete('props/{id}', [PropController::class, 'destroy'])
        ->name('props.destroy')
        ->middleware('permission:props.destroy');

    Route::put('props/{id}', [PropController::class, 'update'])
        ->name('props.update')
        ->middleware('permission:props.edit');

    Route::get('props/{id}/edit', [PropController::class, 'edit'])
        ->name('props.edit')
        ->middleware('permission:props.edit');

    Route::post('props', [PropController::class, 'store'])
        ->name('props.store')
        ->middleware('permission:props.create');

    Route::get('props/create', [PropController::class, 'create'])
        ->name('props.create')
        ->middleware('permission:props.create');

    Route::get('props/{id}', [PropController::class, 'show'])
        ->name('props.show')
        ->middleware('permission:props.show');

    Route::get('props', [PropController::class, 'index'])
        ->name('props.index')
        ->middleware('permission:props.index');
});
