<?php

namespace Devcbh\SmsWrapper\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Devcbh\SmsWrapper\Facades\Sms;
use Devcbh\SmsWrapper\SmsServiceProvider;

class SmsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Sms' => Sms::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('sms.macrokiosk.domain', 'https://api.macrokiosk.test');
        $app['config']->set('sms.macrokiosk.override_pass', 'test-pass');
    }

    public function test_it_can_send_a_single_sms()
    {
        Http::fake([
            'https://api.macrokiosk.test' => Http::response(['status' => 'success'], 200),
        ]);

        $response = Sms::send('09990000000', 'TEST');

        $this->assertTrue($response->successful());
        $this->assertEquals(['status' => 'success'], $response->json());

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://api.macrokiosk.test' &&
                   $request['type'] === 'otp' &&
                   $request['provider'] === 'macrokiosk' &&
                   $request['mobile_number'] === '09990000000' &&
                   $request['message'] === 'TEST' &&
                   $request['override_pass'] === 'test-pass';
        });
    }

    public function test_it_can_send_concurrent_sms()
    {
        Http::fake([
            'https://api.macrokiosk.test' => Http::response(['status' => 'success'], 200),
        ]);

        $messages = [
            ['mobile_number' => '09990000000', 'message' => 'Msg 1'],
            ['mobile_number' => '09990000000', 'message' => 'Msg 2'],
        ];

        $responses = Sms::sendConcurrent($messages);

        $this->assertCount(2, $responses);
        $this->assertTrue($responses[0]->successful());
        $this->assertTrue($responses[1]->successful());

        Http::assertSentCount(2);
    }
}
