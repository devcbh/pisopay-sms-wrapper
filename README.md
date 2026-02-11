# SMS Wrapper for Pisopay

A Laravel wrapper for sending SMS via Macrokiosk, built for speed and concurrency.

## Installation

```bash
composer require devcbh/sms-wrapper
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Devcbh\SmsWrapper\SmsServiceProvider" --tag="config"
```

Then, set your environment variables in `.env`:

```env
PISOPAY_SMS_DOMAIN=https://your-api-domain.com
PISOPAY_SMS_OVERRIDE_PASS=your-override-pass
```

## Usage

### Sending a Single SMS

```php
use Devcbh\SmsWrapper\Facades\Sms;

$response = Sms::send('09990000000', 'Hello Team');

if ($response->successful()) {
    // SMS sent successfully
}
```

### Sending Concurrent SMS (Blazing Fast)

```php
use Devcbh\SmsWrapper\Facades\Sms;

$messages = [
    ['mobile_number' => '09990000000', 'message' => 'OTP: 1234'],
    ['mobile_number' => '09990000000', 'message' => 'OTP: 5678'],
];

$responses = Sms::sendConcurrent($messages);

foreach ($responses as $response) {
    if ($response->successful()) {
        // Handle success
    }
}
```

