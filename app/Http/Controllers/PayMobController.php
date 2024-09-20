<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\SendSmsAndEmail;
use IslamAlsayed\PayMob\PayMob;

class PayMobController extends Controller
{
    use SendSmsAndEmail;

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

        return $PaymentKey->token;
    }

    public function checkout_processed(Request $request)
    {
        $request_hmac = $request->hmac;
        $calc_hmac = PayMob::calcHMAC($request);

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
                $this->SendSmsAndEmail($order);
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
