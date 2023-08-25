<?php

use App\Http\Modules\Categories\Models\Category;
use App\Http\Modules\Cities\Controllers\CityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Cities Routes
|--------------------------------------------------------------------------
*/

Route::prefix('categories')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
       /*  Route::controller(CityController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:cities-list');
        }); */

        Route::get('list',function(){
            return Category::all();
        });
    });
});
