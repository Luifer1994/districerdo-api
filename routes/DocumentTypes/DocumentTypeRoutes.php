<?php

use App\Http\Modules\DocumentTypes\Controllers\DocumentTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document types Routes
|--------------------------------------------------------------------------
*/

Route::prefix('document-types')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(DocumentTypeController::class)->group(function () {
            Route::get('list', 'index')/* ->middleware('permission:document-types-list') */;
        });
    });
});
