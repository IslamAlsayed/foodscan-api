<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProjectsController;
use Faker\Guesser\Name;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn() => view('payment.checkout'));

Route::post('/checkout', [OrderController::class, 'store'])->name('checkout_post');

Route::get('/checkout/response', function (Request $request) {
    if ($request->success == false) {
        return view('payment.checkout')->with(['danger' => 'Unfortunately, your payment could not be processed. Please try again later.']);
    }

    return view('payment.checkout')->with(['success' => 'Your payment has been successfully processed. Thank you!']);
});
