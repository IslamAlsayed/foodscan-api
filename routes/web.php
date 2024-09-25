<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|-------------
| Web Routes |
|-------------
*/

Route::get('/', fn() => view('welcome'));

Route::get('/checkout/response', function (Request $request) {
    $message = [];
    if ($request->success == false) $message = ['danger' => 'Unfortunately, your payment could not be processed. Please try again later.'];
    $message = ['success' => 'Your payment has been successfully processed. Thank you!'];

    return view('payment.checkout')->with($message);
});
