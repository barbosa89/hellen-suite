<?php

use Illuminate\Support\Facades\Route;

Route::get('invoices/{number}/payments/confirm', 'InvoiceController@confirmPayment')
    ->name('invoices.payments.confirm')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::delete('invoices/{invoice}', 'InvoiceController@destroy')
    ->name('invoices.destroy')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::post('invoices', 'InvoiceController@store')
    ->name('invoices.store')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::get('invoices/{invoice}', 'InvoiceController@show')
    ->name('invoices.show')
    ->middleware(['auth', 'verified', 'role:manager']);

Route::get('invoices', 'InvoiceController@index')
    ->name('invoices.index')
    ->middleware(['auth', 'verified', 'role:manager']);
