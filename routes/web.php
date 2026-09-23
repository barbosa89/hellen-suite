<?php

declare(strict_types=1);

use App\Http\Controllers\CancelReservationController;
use App\Http\Controllers\CheckInReservationController;
use App\Http\Controllers\CheckOutStayController;
use App\Http\Controllers\ConfirmReservationController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestLookupController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelImageController;
use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MarkReservationNoShowController;
use App\Http\Controllers\ReservationAvailabilityController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StayController;
use App\Http\Controllers\StayGuestController;
use App\Http\Controllers\TransferRoomOccupancyController;
use App\Http\Controllers\UpdateRoomHousekeepingStatusController;
use App\Http\Controllers\UpdateStayExpectedCheckOutController;
use App\Http\Middleware\EnsureCurrencyConfigured;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

Route::get('/', function () {
    return redirect()->route('hotels.index');
});

Route::resource('hotels', HotelController::class);

Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/hotels/{hotel}/management', [HotelManagementController::class, 'index'])->name('hotels.management.index');
Route::get('/hotels/{hotel}/image', [HotelImageController::class, 'show'])->name('hotels.image');
Route::resource('hotels.room-types', RoomTypeController::class)
    ->except('show')
    ->scoped();

Route::resource('hotels.rooms', RoomController::class)
    ->except('show')
    ->scoped()
    ->middleware(EnsureCurrencyConfigured::class);

Route::get('/hotels/{hotel}/guests/lookup', GuestLookupController::class)
    ->name('hotels.guests.lookup');

Route::resource('hotels.guests', GuestController::class)
    ->except('destroy')
    ->scoped();

Route::resource('hotels.stays', StayController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->scoped()
    ->middleware(EnsureCurrencyConfigured::class);

Route::get('/hotels/{hotel}/reservations/availability/{reservation?}', ReservationAvailabilityController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.reservations.availability');

Route::resource('hotels.reservations', ReservationController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
    ->scoped()
    ->middleware(EnsureCurrencyConfigured::class);

Route::patch('/hotels/{hotel}/reservations/{reservation}/confirm', ConfirmReservationController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.reservations.confirm');

Route::patch('/hotels/{hotel}/reservations/{reservation}/cancel', CancelReservationController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.reservations.cancel');

Route::patch('/hotels/{hotel}/reservations/{reservation}/no-show', MarkReservationNoShowController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.reservations.no-show');

Route::post('/hotels/{hotel}/reservations/{reservation}/check-in', CheckInReservationController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.reservations.check-in');

Route::patch('/hotels/{hotel}/stays/{stay}/expected-check-out', UpdateStayExpectedCheckOutController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.stays.expected-check-out.update');

Route::post('/hotels/{hotel}/stays/{stay}/check-out', CheckOutStayController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.stays.check-out');

Route::post('/hotels/{hotel}/stays/{stay}/guests', StayGuestController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.stays.guests.store');

Route::post('/hotels/{hotel}/stays/{stay}/room-occupancies/{roomOccupancy}/transfer', TransferRoomOccupancyController::class)
    ->scopeBindings()
    ->middleware(EnsureCurrencyConfigured::class)
    ->name('hotels.stays.room-occupancies.transfer');

Route::patch('/hotels/{hotel}/rooms/{room}/toggle', [RoomController::class, 'toggle'])
    ->scopeBindings()
    ->name('hotels.rooms.toggle');

Route::patch('/hotels/{hotel}/rooms/{room}/housekeeping-status', UpdateRoomHousekeepingStatusController::class)
    ->scopeBindings()
    ->name('hotels.rooms.housekeeping-status.update');

Route::get('/dashboard', function () {
    return redirect()->route('hotels.index');
})->name('dashboard');
