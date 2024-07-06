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
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SubscriberController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/accounts/verify/{email}/{token}', [AccountController::class, 'verify'])
    ->name('accounts.verify')
    ->middleware('signed');

Route::post('/accounts/password', [AccountController::class, 'updatePassword'])
    ->name('accounts.password.update')
    ->middleware('auth');

Route::get('/accounts/password', [AccountController::class, 'changePassword'])
    ->name('accounts.password.change')
    ->middleware('auth');

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('language/{locale}', [LanguageController::class, 'locale']);

Route::post('/subscribe', [SubscriberController::class, 'subscribe'])
    ->name('subscribe')
    ->middleware(['sanitize', 'honeypot']);

Route::get('/unsubscribe/{email}', [SubscriberController::class, 'unsubscribe'])
    ->name('unsubscribe');

Route::post('/message', [ContactController::class, 'message'])
    ->name('message')
    ->middleware(['sanitize', 'honeypot']);

require __DIR__ . '/root.php';

require __DIR__ . '/api/web/notes.php';
require __DIR__ . '/api/web/vouchers.php';
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
require __DIR__ . '/modules/asset_maintenances.php';
require __DIR__ . '/modules/props.php';
require __DIR__ . '/modules/vehicles.php';
require __DIR__ . '/modules/payments.php';
require __DIR__ . '/modules/dining.php';
require __DIR__ . '/modules/shifts.php';
require __DIR__ . '/modules/tags.php';
require __DIR__ . '/modules/notes.php';
require __DIR__ . '/modules/plans.php';
require __DIR__ . '/modules/invoices.php';


