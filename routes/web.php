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
    $user = \App\User::where('id', 5)->with(['parent', 'roles'])->first();
    $password = str_random(8);

    return view('emails.welkome.admin', compact('user', 'password'));
});

Route::get('/', function () {
    if (config('welkome.env') == 'desktop') {
        return redirect(route('login'));
    }

    return view('welcome');
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
    ->middleware('auth');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('language/{locale}', 'LanguageController@locale');

require __DIR__ . '/root.php';
// require __DIR__ . '/admin.php';
require __DIR__ . '/common.php'; 


