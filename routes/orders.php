<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|-----------------------
| API Routes To Orders |
|-----------------------
*/

Route::group(['middleware' => ['auth:admin-api', 'CheckAdminToken']], function () {
    Route::post('orders/store', 'OrderController@store');
    Route::post('orders/update/{id}', 'OrderController@update');
    Route::put('orders/active/{id}', 'OrderController@updateStatus');
    Route::put('orders/status/{id}', 'OrderController@updateOrderStatus');
    Route::delete('orders/{id}', 'OrderController@destroy');
});

Route::get('orders', 'OrderController@index');
Route::get('orders/show/{id}', 'OrderController@show');
