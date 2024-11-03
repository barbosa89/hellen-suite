<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('invoices/{number}/payments/confirm', [InvoiceController::class, 'confirmPayment'])
    ->name('invoices.payments.confirm')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])
    ->name('invoices.destroy')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::post('invoices', [InvoiceController::class, 'store'])
    ->name('invoices.store')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
    ->name('invoices.show')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::get('invoices', [InvoiceController::class, 'index'])
    ->name('invoices.index')
    ->middleware(['auth', 'verified', 'role:manager']);
