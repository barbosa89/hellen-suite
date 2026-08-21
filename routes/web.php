<?php

declare(strict_types=1);

use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelImageController;
use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::get('/', function () {
    return redirect()->route('hotels.index');
});

Route::resource('hotels', HotelController::class);

Route::get('/hotels/{hotel}/management', [HotelManagementController::class, 'index'])->name('hotels.management.index');
Route::get('/hotels/{hotel}/image', [HotelImageController::class, 'show'])->name('hotels.image');
Route::get('/hotels/{hotel}/rooms', [RoomController::class, 'index'])->name('hotels.rooms.index');

Route::get('/dashboard', function () {
    return redirect()->route('hotels.index');
})->name('dashboard');
