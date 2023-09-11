<?php

use App\Http\Modules\Purchases\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| purchases Routes
|--------------------------------------------------------------------------
*/

Route::prefix('purchases')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(PurchaseController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:purchases-list');
            Route::post('create', 'store')->middleware('permission:purchases-create');
            Route::get('show/{id}', 'show')->middleware('permission:purchases-show');
            Route::put('paid/{id}', 'paid')->middleware('permission:purchases-update');
        });
    });
});
