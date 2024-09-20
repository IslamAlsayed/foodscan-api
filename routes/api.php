<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayMobController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\CustomerController;
use App\Http\Controllers\Auth\EmployeeController;
use App\Http\Controllers\Auth\AdministratorController;

/*
|-------------
| API Routes |
|-------------
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:admin-api', 'CheckAdminToken']);

Route::group(['middleware' => ['auth:admin-api', 'CheckAdminToken']], function () {
    Route::post('administrators/store', [AdministratorController::class, 'store']);
    Route::post('administrators/update/{id}', [AdministratorController::class, 'update']);
    Route::put('administrators/active/{id}', [AdministratorController::class, 'updateStatus']);
    Route::delete('administrators/{id}', [AdministratorController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:employee-api'], function () {
    Route::post('employees/store', [EmployeeController::class, 'store']);
    Route::post('employees/update/{id}', [EmployeeController::class, 'update']);
    Route::put('employees/active/{id}', [EmployeeController::class, 'updateStatus']);
    Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
});

Route::group(['middleware' => 'auth:customer-api'], function () {
    Route::post('customers/store', [CustomerController::class, 'store']);
    Route::post('customers/update/{id}', [CustomerController::class, 'update']);
    Route::put('customers/active/{id}', [CustomerController::class, 'updateStatus']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
});

Route::get('administrators', [AdministratorController::class, 'index']);
Route::get('administrators/show/{id}', [AdministratorController::class, 'show']);

Route::get('employees', [EmployeeController::class, 'index']);
Route::get('employees/show/{id}', [EmployeeController::class, 'show']);

Route::get('customers', [CustomerController::class, 'index']);
Route::get('customers/show/{id}', [CustomerController::class, 'show']);

require __DIR__ . '/items.php';
require __DIR__ . '/categories.php';
require __DIR__ . '/diningTables.php';
require __DIR__ . '/orders.php';

Route::post('checkout/processed', [PayMobController::class, 'checkout_processed']);
