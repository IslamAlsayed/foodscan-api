<?php

namespace App\Observers;

use App\Models\Meal;
use App\Models\Addon;
use App\Models\Extra;
use App\Models\Order;
use App\Mail\MailOrder;
use App\Services\SMSMessage;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    protected $products = [
        ['id' => 1, 'quantity' => 7],
        ['id' => 3, 'quantity' => 7],
        ['id' => 3, 'quantity' => 7],
        ['addon_id' => 3, 'quantity' => 3],
        ['addon_id' => 3, 'quantity' => 3],
        ['addon_id' => 3, 'quantity' => 3],
        ['addon_id' => 3, 'quantity' => 3],
        ['addon_id' => 3, 'quantity' => 3],
        ['extra_id' => 9, 'quantity' => 3]
    ];

    public function created(Order $order): void
    {
        // $products = json_decode($this->products, true);
        $total = 0;

        foreach ($this->products as $product) {
            if (isset($product['id'])) {
                $meal = Meal::find($product['id']);
                if ($meal) {
                    $total += $meal->price * $product['quantity'];
                }
            }

            if (isset($product['addon_id'])) {
                $addon = Addon::find($product['addon_id']);
                if ($addon) {
                    $total += $addon->price * $product['quantity'];
                }
            }

            if (isset($product['extra_id'])) {
                $extra = Extra::find($product['extra_id']);
                if ($extra) {
                    $total += $extra->price * $product['quantity'];
                }
            }
        }
        $order->total = $total;
        $order->order_status = 'in_progress';

        $order->save();
    }
}