<?php

namespace Devcbh\SmsWrapper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Http\Client\Response send(string $mobileNumber, string $message, string $type = 'otp', string $provider = 'macrokiosk')
 * @method static array sendConcurrent(array $messages)
 * 
 * @see \Devcbh\SmsWrapper\SmsManager
 */
class Sms extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sms-wrapper';
    }
}
