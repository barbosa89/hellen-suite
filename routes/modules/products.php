<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVoucherController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'verified']], function (): void {
    // Products report

    Route::post('products/report', [ProductController::class, 'exportReport'])
        ->name('products.report.export')
        ->middleware('permission:products.index');

    Route::get('products/report', [ProductController::class, 'showReportForm'])
        ->name('products.report')
        ->middleware('permission:products.index');

    Route::post('products/{id}/report', [ProductController::class, 'exportProductReport'])
        ->name('products.product.report.export')
        ->middleware('permission:products.index');

    Route::get('products/{id}/report', [ProductController::class, 'showProductReportForm'])
        ->name('products.product.report')
        ->middleware('permission:products.index');

    // Products vouchers routes

    Route::get('products/{id}/vouchers/{voucher}', [ProductVoucherController::class, 'destroy'])
        ->name('products.vouchers.destroy')
        ->middleware('permission:products.vouchers');

    Route::post('products/vouchers', [ProductVoucherController::class, 'store'])
        ->name('products.vouchers')
        ->middleware('permission:products.vouchers');

    Route::get('products/vouchers', [ProductVoucherController::class, 'create'])
        ->name('products.vouchers.create')
        ->middleware('permission:products.vouchers');

    // Products routes

    Route::get('products/search', [ProductController::class, 'search'])
        ->name('products.search')
        ->middleware('permission:products.index');

    Route::post('products/calculate/total', [ProductController::class, 'total'])
        ->name('products.total')
        ->middleware('permission:products.index');

    Route::get('products/{id}/toggle', [ProductController::class, 'toggle'])
        ->name('products.toggle')
        ->middleware('permission:products.edit');

    Route::delete('products/{id}', [ProductController::class, 'destroy'])
        ->name('products.destroy')
        ->middleware('permission:products.destroy');

    Route::put('products/{id}', [ProductController::class, 'update'])
        ->name('products.update')
        ->middleware('permission:products.edit');

    Route::get('products/{id}/edit', [ProductController::class, 'edit'])
        ->name('products.edit')
        ->middleware('permission:products.edit');

    Route::post('products', [ProductController::class, 'store'])
        ->name('products.store')
        ->middleware('permission:products.create');

    Route::get('products/create', [ProductController::class, 'create'])
        ->name('products.create')
        ->middleware('permission:products.create');

    Route::get('products/{id}', [ProductController::class, 'show'])
        ->name('products.show')
        ->middleware('permission:products.show');

    Route::get('products', [ProductController::class, 'index'])
        ->name('products.index')
        ->middleware('permission:products.index');
});
