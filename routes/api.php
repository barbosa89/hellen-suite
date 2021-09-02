<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware(['auth:api', 'verified'])
    ->group(function () {
        require __DIR__ . '/api/guests.php';
        require __DIR__ . '/api/countries.php';
        require __DIR__ . '/api/identification_types.php';
    });
