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
            Route::get('list', 'index')->middleware('permission:invoices-list');
            Route::post('create', 'store')->middleware('permission:invoices-create');
            Route::get('show/{id}', 'show')->middleware('permission:invoices-show');
            Route::put('paid/{id}', 'paid')->middleware('permission:invoices-paid');
            Route::put('cancel/{id}', 'cancel')->middleware('permission:invoices-cancel');
            Route::get('download/{id}', 'download')->middleware('permission:invoices-download');
            Route::get('total-amount-for-month', 'totalAmountForMonth');
            Route::post('partial-payment', 'partialPayment')->middleware('permission:invoices-partial-payment');
            Route::get('download-evidence/{id}', 'downloadEvidence')->middleware('permission:invoices-download-evidence');
        });
    });
});
