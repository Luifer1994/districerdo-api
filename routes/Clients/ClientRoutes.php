<?php

use App\Http\Modules\Clients\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| clients Routes
|--------------------------------------------------------------------------
*/

Route::prefix('clients')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(ClientController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:clients-list');
            Route::get('show/{id}', 'show')->middleware('permission:clients-show');
            Route::post('create', 'store')->middleware('permission:clients-create');
            Route::put('update/{id}', 'update')->middleware('permission:clients-update');
            Route::get('search', 'searchClients')/* ->middleware('permission:clients-search') */;
        });
    });
});
