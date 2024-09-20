<?php

namespace App\Services;

use Twilio\Rest\Client;

class SMSMessage
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }

    public function sendSMS($to, $message)
    {
        $from = env('TWILIO_PHONE_NUMBER');

        try {
            $this->client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}