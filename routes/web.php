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

    function point($s1, $s2) {
        $p1 = $s1;
        $p2 = $s2;

        while ($p1 != $p2) {
            $p1 += array_sum(str_split($p1));
            $p2 += array_sum(str_split($p2));
        }

        return [$p1, $p2];
    }

    dd(point(471, 480), str_split(480));
});

Route::get('/', function () {
    if (config('welkome.env') == 'desktop') {
        return redirect(route('login'));
    }

    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('language/{locale}', 'LanguageController@locale');

require __DIR__ . '/common.php';
require __DIR__ . '/receptionist.php';
require __DIR__ . '/root.php';
require __DIR__ . '/admin.php';

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
