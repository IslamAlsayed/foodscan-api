<?php

namespace App\Traits;

use App\Mail\MailOrder;
use App\Services\SMSMessage;
use Illuminate\Support\Facades\Mail;

trait SendSmsAndEmail
{
    public function SendSmsAndEmail($order)
    {
        Mail::to($order->customer->email)->send(new MailOrder($order));

        $SMSMessage = new SMSMessage();
        $websiteUrl = 'http://192.168.1.63:8000/api/orders/show/' . $order->id;

        $message =
            $order->order_status === 'in_progress' ?
            "Your order is currently in progress. We are working hard to get it completed as soon as possible. Please review the updated details below. Visit us at $websiteUrl for more details."
            : ($order->order_status === 'cancelled' ?
                "We regret to inform you that your order has been cancelled. Please review the details below and contact us if you have any questions or concerns. Visit us at $websiteUrl for more details."
                : ($order->order_status === 'done' ?
                    "Your order is now ready for pickup! 🎁 Please visit us to pick it up. We appreciate your patience! Visit us at $websiteUrl for more details."
                    : "Your order has been updated. Please review the updated details below. Visit us at $websiteUrl for more details."));

        $SMSMessage->sendSMS('+2001065438133', $message);
    }
}
