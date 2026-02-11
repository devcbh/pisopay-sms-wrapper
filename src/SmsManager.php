<?php

namespace Devcbh\SmsWrapper;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class SmsManager
{
    protected string $domain;
    protected string $overridePass;

    public function __construct()
    {
        $this->domain = Config::get('sms.domain');
        $this->overridePass = Config::get('sms.override_pass');
    }

    /**
     * Send a single SMS.
     *
     * @param string $mobileNumber
     * @param string $message
     * @param string $type
     * @param string $provider
     * @return \Illuminate\Http\Client\Response
     */
    public function send(string $mobileNumber, string $message, string $provider = 'macrokiosk', string $type = 'otp')
    {
        return Http::post($this->domain, [
            'type' => $type,
            'provider' => $provider,
            'mobile_number' => $mobileNumber,
            'message' => $message,
            'override_pass' => $this->overridePass,
        ]);
    }

    /**
     * Send multiple SMS concurrently.
     *
     * @param array $messages Array of arrays containing 'mobile_number', 'message', and optionally 'type', 'provider'
     * @return array
     */
    public function sendConcurrent(array $messages): array
    {
        $responses = Http::pool(fn (Pool $pool) => 
            collect($messages)->map(fn ($msg) => 
                $pool->post($this->domain, [
                    'type' => $msg['type'] ?? 'otp',
                    'provider' => $msg['provider'] ?? 'macrokiosk',
                    'mobile_number' => $msg['mobile_number'],
                    'message' => $msg['message'],
                    'override_pass' => $this->overridePass,
                ])
            )->toArray()
        );

        return $responses;
    }
}
