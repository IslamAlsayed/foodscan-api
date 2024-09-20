<?php

use App\Http\Controllers\AddonController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\MealController;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------
| API Routes To [Meals, Addons, Extras] |
|----------------------------------------
*/

Route::group(['middleware' => ['auth:admin-api', 'CheckAdminToken']], function () {
    Route::controller(MealController::class)->group(function () {
        Route::get('meals', 'index');
        Route::post('meals/store', 'store');
        Route::post('meals/update/{id}', 'update');
        Route::put('meals/active/{id}', 'updateStatus');
        Route::delete('meals/{id}', 'destroy');
        Route::post('meals/search', 'search');
    });


    Route::controller(AddonController::class)->group(function () {
        Route::get('addons', 'index');
        Route::post('addons/store', 'store');
        Route::post('addons/update/{id}', 'update');
        Route::put('addons/active/{id}', 'updateStatus');
        Route::delete('addons/{id}', 'destroy');
    });

    Route::controller(ExtraController::class)->group(function () {
        Route::get('extras', 'index');
        Route::post('extras/store', 'store');
        Route::post('extras/update/{id}', 'update');
        Route::put('extras/active/{id}', 'updateStatus');
        Route::delete('extras/{id}', 'destroy');
    });
});

Route::get('meals', 'MealController@index');
Route::get('meals/show/{id}', 'MealController@show');

Route::get('addons', 'AddonController@index');
Route::get('addons/show/{id}', 'AddonController@show');

Route::get('extras', 'ExtraController@index');
Route::get('extras/show/{id}', 'ExtraController@show');
