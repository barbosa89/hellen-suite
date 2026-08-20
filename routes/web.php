<?php

declare(strict_types=1);

use App\Http\Controllers\HotelController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::get('/', function () {
    return redirect()->route('hotels.index');
});

Route::resource('hotels', HotelController::class);
use App\Http\Controllers\HotelImageController;
Route::get('/hotels/{hotel}/image', [HotelImageController::class, 'show'])->name('hotels.image');
Route::get('/hotels/{hotel}/rooms', [RoomController::class, 'index'])->name('hotels.rooms.index');

Route::get('/dashboard', function () {
    return redirect()->route('hotels.index');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
