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

Route::get('/test', function () {
    $b = false;
    dd(now()->addDay(1));
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

require __DIR__ . '/common.php';
require __DIR__ . '/receptionist.php';
require __DIR__ . '/root.php';
require __DIR__ . '/admin.php';