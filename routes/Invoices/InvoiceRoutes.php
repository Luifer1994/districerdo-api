<?php

use App\Http\Modules\Invoices\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Invoices Routes
|--------------------------------------------------------------------------
*/

Route::prefix('invoices')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(InvoiceController::class)->group(function () {
            Route::get('list', 'index')/* ->middleware('permission:invoices-list') */;
            Route::post('create', 'store')/* ->middleware('permission:invoices-create') */;
        });
    });
});
