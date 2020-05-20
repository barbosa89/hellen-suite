<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    // abort(403);
    $checkin = 'Checking of GUEST, room no. NUMBER, of voucher NUMBER';
    $checkout = 'Checkout of GUEST, room no. NUMBER, of voucher NUMBER';
    // checking(Voucher $voucher)->guest(Guest $guest)->room(Room $room)->withCustomer()->write();
    // checkout(Voucher $voucher)->guest(Guest $guest)->room(Room $room)->withCustomer()->withVehicle()->write();

    // Ingresa vehículo con matrícula PLACA, tipo TIPO, propietario GUEST, voucher NUMBER
    // vehicle(Vehicle $vehicle)->entry()->owner(Guest $guest)->write()

    // Salida del vehículo con matrícula PLACA, tipo TIPO, propietario GUEST, voucher NUMBER
    // vehicle(Vehicle $vehicle)->departure()->owner(Guest $guest)->write()

    // company(Voucher $voucher)->

    dd($checkin, $checkout);
});

Route::get('/', function () {
    if (config('welkome.env') == 'desktop') {
        return redirect(route('login'));
    }

    return view('landing');
});

Route::get('/account/verify/{email}/{token}', 'AccountController@verify')
    ->name('account.verify')
    ->middleware(['guest', 'signed']);

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('language/{locale}', 'LanguageController@locale');

require __DIR__ . '/root.php';

// Modules
require __DIR__ . '/modules/hotels.php';
require __DIR__ . '/modules/rooms.php';
require __DIR__ . '/modules/team.php';
require __DIR__ . '/modules/guests.php';
require __DIR__ . '/modules/companies.php';
require __DIR__ . '/modules/vouchers.php';
require __DIR__ . '/modules/products.php';
require __DIR__ . '/modules/services.php';
require __DIR__ . '/modules/assets.php';
require __DIR__ . '/modules/props.php';
require __DIR__ . '/modules/vehicles.php';
require __DIR__ . '/modules/payments.php';
require __DIR__ . '/modules/dining.php';
require __DIR__ . '/modules/shifts.php';
require __DIR__ . '/modules/tags.php';
require __DIR__ . '/modules/notes.php';
