<?php

use App\Http\Modules\RolesAndPermissions\Controllers\PermissionController;
use App\Http\Modules\RolesAndPermissions\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Roles and Permissions
|--------------------------------------------------------------------------
*/

Route::prefix('roles')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('list', 'index')->middleware('permission:list-roles');
            Route::get('show/{id}', 'show')->middleware('permission:show-roles');
            Route::post('asing-permissions', 'asingPermissionsToRole')->middleware('permission:asisgn-permissions-to-roles');
            Route::post('created', 'store')->middleware('permission:create-roles');
            Route::put('updated/{id}', 'update')->middleware('permission:update-roles');
        });
    });
});
Route::prefix('permissions')->group(function () {
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::controller(PermissionController::class)->group(function () {
            Route::get('group-to-permission', 'getGroupPermissionsByGroup')->middleware('permission:list-groups-to-permissions');
            Route::get('list-by-group/{group}/{rolId}', 'getPermissionsByGroup')->middleware('permission:list-permissions-by-group');
        });
    });
});
