<?php

use App\Http\Modules\Providers\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Provider Routes
|--------------------------------------------------------------------------
*/

Route::prefix('providers')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(ProviderController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:providers-list');
            Route::get('show/{id}', 'show')->middleware('permission:providers-show');
            Route::post('create', 'store')->middleware('permission:providers-create');
            Route::put('update/{id}', 'update')->middleware('permission:providers-update');
        });
    });
});
