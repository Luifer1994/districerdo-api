<?php

use App\Http\Modules\Products\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Products Routes
|--------------------------------------------------------------------------
*/

Route::prefix('products')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:products-list');
            Route::get('show/{id}', 'show')->middleware('permission:products-show');
            Route::post('create', 'store')->middleware('permission:products-create');
            Route::put('update/{id}', 'update')->middleware('permission:products-update');
            Route::get('search', 'searchProducts')->middleware('permission:products-search');
            Route::post('validate-stock', 'validateStock')/* ->middleware('permission:products-validate-stock') */;
        });
    });
});
