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
            Route::get('total-amount-for-month', 'totalAmountForMonth');
            Route::post('partial-payment', 'partialPayment')->middleware('permission:purchases-partial-payment');
            Route::get('download-evidence/{id}', 'downloadEvidence')->middleware('permission:purchases-download-evidence');
        });
    });
});
