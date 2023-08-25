<?php

use Illuminate\Support\Facades\Route;
use App\Http\Modules\Users\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/

Route::prefix('users')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:users-list');
            Route::get('show/{id}', 'show')->middleware('permission:users-show');
            Route::post('create', 'store')->middleware('permission:users-create');
            Route::put('update/{id}', 'update')->middleware('permission:users-update');
            Route::post('change-password', 'changePassword');
        });
    });
});
