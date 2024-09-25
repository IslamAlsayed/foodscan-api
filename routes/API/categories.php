<?php

use Illuminate\Support\Facades\Route;

/*
|---------------------------
| API Routes To Categories |
|---------------------------
*/

Route::group(['middleware' => ['auth:admin-api', 'CheckCodeValidity']], function () {
    Route::post('categories/store', 'CategoryController@store');
    Route::post('categories/update/{id}', 'CategoryController@update');
    Route::put('categories/active/{id}', 'CategoryController@updateStatus');
});

Route::delete('categories/{id}', 'CategoryController@destroy')->middleware('CheckCodeValidity');

Route::get('categories', 'CategoryController@index');
Route::get('categories/show/{id}', 'CategoryController@show');
