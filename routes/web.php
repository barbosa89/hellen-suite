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

Route::get('/', 'LandingController@index');

Route::get('/accounts/verify/{email}/{token}', 'AccountController@verify')
    ->name('accounts.verify')
    ->middleware('signed');

Route::post('/accounts/password', 'AccountController@updatePassword')
    ->name('accounts.password.update')
    ->middleware('auth');

Route::get('/accounts/password', 'AccountController@changePassword')
    ->name('accounts.password.change')
    ->middleware('auth');

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('language/{locale}', 'LanguageController@locale');

Route::post('/subscribe', 'SubscriberController@subscribe')
    ->name('subscribe')
    ->middleware(['sanitize', 'honeypot']);

Route::get('/unsubscribe/{email}', 'SubscriberController@unsubscribe')
    ->name('unsubscribe');

Route::post('/message', 'ContactController@message')
    ->name('message')
    ->middleware(['sanitize', 'honeypot']);

require __DIR__ . '/root.php';

require __DIR__ . '/api/web/companies.php';
require __DIR__ . '/api/web/guests.php';
require __DIR__ . '/api/web/rooms.php';

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
require __DIR__ . '/modules/plans.php';
require __DIR__ . '/modules/invoices.php';


