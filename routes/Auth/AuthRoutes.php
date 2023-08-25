<?php

use App\Http\Modules\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
    });

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('logout', 'logout');
        });
    });
});
