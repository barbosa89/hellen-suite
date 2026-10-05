<?php

declare(strict_types=1);

use App\Http\Controllers\CancelReservationController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CashShiftController;
use App\Http\Controllers\CashShiftReportController;
use App\Http\Controllers\CheckInReservationController;
use App\Http\Controllers\CheckOutRoomOccupancyController;
use App\Http\Controllers\CheckOutStayController;
use App\Http\Controllers\CloseCashShiftController;
use App\Http\Controllers\ConfirmReservationController;
use App\Http\Controllers\FolioAdjustmentController;
use App\Http\Controllers\FolioChargeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestLookupController;
use App\Http\Controllers\HandOverCashShiftController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelImageController;
use App\Http\Controllers\HotelManagementController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MarkReservationNoShowController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentSupportController;
use App\Http\Controllers\PaymentVoucherController;
use App\Http\Controllers\RefundPaymentController;
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
use App\Http\Middleware\EnsureAppConfigured;
use Illuminate\Support\Facades\Route;

Route::post('/locale', [LocaleController::class, 'update'])
    ->withoutMiddleware(EnsureAppConfigured::class)
    ->name('locale.update');

Route::get('/', function () {
    return redirect()->route('hotels.index');
});

Route::resource('hotels', HotelController::class);

Route::get('settings', [SettingsController::class, 'edit'])
    ->withoutMiddleware(EnsureAppConfigured::class)
    ->name('settings.edit');
Route::put('settings', [SettingsController::class, 'update'])
    ->withoutMiddleware(EnsureAppConfigured::class)
    ->name('settings.update');

Route::get('/hotels/{hotel}/management', [HotelManagementController::class, 'index'])->name('hotels.management.index');
Route::get('/hotels/{hotel}/image', [HotelImageController::class, 'show'])->name('hotels.image');
Route::resource('hotels.room-types', RoomTypeController::class)
    ->except('show')
    ->scoped();

Route::resource('hotels.rooms', RoomController::class)
    ->except('show')
    ->scoped();

Route::get('/hotels/{hotel}/guests/lookup', GuestLookupController::class)
    ->name('hotels.guests.lookup');

Route::resource('hotels.guests', GuestController::class)
    ->except('destroy')
    ->scoped();

Route::resource('hotels.stays', StayController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->scoped();

Route::get('/hotels/{hotel}/reservations/availability/{reservation?}', ReservationAvailabilityController::class)
    ->scopeBindings()
    ->name('hotels.reservations.availability');

Route::resource('hotels.reservations', ReservationController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
    ->scoped();

Route::patch('/hotels/{hotel}/reservations/{reservation}/confirm', ConfirmReservationController::class)
    ->scopeBindings()
    ->name('hotels.reservations.confirm');

Route::patch('/hotels/{hotel}/reservations/{reservation}/cancel', CancelReservationController::class)
    ->scopeBindings()
    ->name('hotels.reservations.cancel');

Route::patch('/hotels/{hotel}/reservations/{reservation}/no-show', MarkReservationNoShowController::class)
    ->scopeBindings()
    ->name('hotels.reservations.no-show');

Route::post('/hotels/{hotel}/reservations/{reservation}/check-in', CheckInReservationController::class)
    ->scopeBindings()
    ->name('hotels.reservations.check-in');

Route::patch('/hotels/{hotel}/stays/{stay}/expected-check-out', UpdateStayExpectedCheckOutController::class)
    ->scopeBindings()
    ->name('hotels.stays.expected-check-out.update');

Route::post('/hotels/{hotel}/stays/{stay}/check-out', CheckOutStayController::class)
    ->scopeBindings()
    ->name('hotels.stays.check-out');

Route::post('/hotels/{hotel}/stays/{stay}/guests', StayGuestController::class)
    ->scopeBindings()
    ->name('hotels.stays.guests.store');

Route::post('/hotels/{hotel}/stays/{stay}/room-occupancies/{roomOccupancy}/transfer', TransferRoomOccupancyController::class)
    ->scopeBindings()
    ->name('hotels.stays.room-occupancies.transfer');

Route::post('/hotels/{hotel}/stays/{stay}/room-occupancies/{roomOccupancy}/check-out', CheckOutRoomOccupancyController::class)
    ->scopeBindings()
    ->name('hotels.stays.room-occupancies.check-out');

Route::post('/hotels/{hotel}/stays/{stay}/folios/{stayFolio}/payments', PaymentController::class)
    ->name('hotels.stays.folios.payments.store');

Route::post('/hotels/{hotel}/stays/{stay}/folios/{stayFolio}/charges', FolioChargeController::class)
    ->name('hotels.stays.folios.charges.store');

Route::post('/hotels/{hotel}/stays/{stay}/folios/{stayFolio}/adjustments', FolioAdjustmentController::class)
    ->name('hotels.stays.folios.adjustments.store');

Route::post('/hotels/{hotel}/stays/{stay}/payments/{payment}/refund', RefundPaymentController::class)
    ->name('hotels.stays.payments.refund');

Route::get('/hotels/{hotel}/stays/{stay}/payments/{payment}/support', PaymentSupportController::class)
    ->name('hotels.stays.payments.support');

Route::get('/hotels/{hotel}/stays/{stay}/payments/{payment}/voucher/{format}', PaymentVoucherController::class)
    ->name('hotels.stays.payments.voucher');

Route::get('/hotels/{hotel}/cash', [CashController::class, 'index'])->name('hotels.cash.index');
Route::post('/hotels/{hotel}/cash', [CashController::class, 'store'])->name('hotels.cash.store');
Route::post('/hotels/{hotel}/cash/shifts', [CashShiftController::class, 'store'])->name('hotels.cash.shifts.store');
Route::get('/hotels/{hotel}/cash/shifts/{cashShift}', [CashShiftController::class, 'show'])->name('hotels.cash.shifts.show');
Route::post('/hotels/{hotel}/cash/shifts/{cashShift}/close', CloseCashShiftController::class)->name('hotels.cash.shifts.close');
Route::post('/hotels/{hotel}/cash/shifts/{cashShift}/handover', HandOverCashShiftController::class)->name('hotels.cash.shifts.handover');
Route::get('/hotels/{hotel}/cash/shifts/{cashShift}/report/{format}', CashShiftReportController::class)->name('hotels.cash.shifts.report');

Route::patch('/hotels/{hotel}/rooms/{room}/toggle', [RoomController::class, 'toggle'])
    ->scopeBindings()
    ->name('hotels.rooms.toggle');

Route::patch('/hotels/{hotel}/rooms/{room}/housekeeping-status', UpdateRoomHousekeepingStatusController::class)
    ->scopeBindings()
    ->name('hotels.rooms.housekeeping-status.update');

Route::get('/dashboard', function () {
    return redirect()->route('hotels.index');
})->name('dashboard');
