<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Jobs\SendOrderEmailJob;
use IslamAlsayed\PayMob\PayMob;
use Illuminate\Support\Facades\Log;

/**
 * Handles payment processing using PayMob API.
 *
 * @author IslamAlsayed eslamalsayed8133@gmail.com
 */
class PayMobController extends Controller
{
    /**
     * This function is responsible for processing the payment using PayMob API.
     *
     * @param Order $order The order object containing the details of the order to be paid.
     * @return JsonResponse Returns a JSON response with the payment status and token.
     * @throws Exception Throws an exception if any error occurs during the payment process.
     */
    public static function pay($order)
    {
        $auth = PayMob::AuthenticationRequest();

        $orderPayMob = PayMob::OrderRegistrationAPI([
            'auth_token' => $auth->token,
            'amount_cents' => $order->total * 100,
            'currency' => 'EGP',
            'delivery_needed' => false,
            'merchant_order_id' => $order->id,
            'items' => []
        ]);

        if (isset($orderPayMob->message) && $orderPayMob->message == 'duplicate') {
            Log::info(json_encode($orderPayMob));
            return response()->json(['status' => 'failed', 'message' => 'Order already registered'], 400);
        }

        $PaymentKey = PayMob::PaymentKeyRequest([
            'auth_token' => $auth->token,
            'amount_cents' => $order->total * 100,
            'currency' => 'EGP',
            'order_id' => $orderPayMob->id,
            "billing_data" => [
                "apartment" => "803",
                "email" => $order->customer->email,
                "floor" => "42",
                "first_name" => $order->customer->name,
                "street" => "Ethan Land",
                "building" => "8028",
                "phone_number" => $order->customer->phone,
                "shipping_method" => "PKG",
                "postal_code" => "01898",
                "city" => "Jaskolskiburgh",
                "country" => "CR",
                "last_name" => "Nicolas",
                "state" => "Utah"
            ]
        ]);

        return response()->json(['status' => 'success', 'token' => $PaymentKey->token], 200);
    }

    /**
     * Processes the payment checkout response from PayMob API.
     *
     * This function is responsible for validating the HMAC signature, retrieving the order details,
     * and updating the order status based on the payment response.
     *
     * @param Request $request The request object containing the payment response data.
     * @return void
     */
    public function checkout_processed(Request $request)
    {
        $request_hmac = $request->hmac;
        $calc_hmac = PayMob::calculationHMAC($request);

        if ($request_hmac == $calc_hmac) {
            $order_id = $request->obj['order']['merchant_order_id'];
            $amount_cents = $request->obj['amount_cents'];
            $transaction_id = $request->obj['id'];

            $order = Order::findOrFail($order_id);

            if ($request->obj['success'] == true && ($order->total * 100) == $amount_cents) {
                $order->update([
                    'payment_type' => 'online',
                    'payment_status' => 'paid',
                    'transaction_id' => $transaction_id
                ]);
                // SendOrderEmailJob::dispatch($order);
            } else {
                $order->update([
                    'payment_type' => 'online',
                    'payment_status' => 'unpaid',
                    'transaction_id' => $transaction_id
                ]);
            }
        }
    }
}