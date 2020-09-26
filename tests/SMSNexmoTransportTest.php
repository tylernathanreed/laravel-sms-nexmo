<?php

namespace Reedware\LaravelSMS\Tests\Nexmo;

use Nexmo\Client;
use Psr\Http\Client\ClientInterface;
use Reedware\LaravelSMS\Nexmo\NexmoTransport;

class SMSNexmoTransportTest extends TestCase
{
    public function testGetNexmoTransportWithConfiguredClient()
    {
        $this->app['config']->set('sms.default', 'nexmo');
        $this->app['config']->set('sms.providers.nexmo.transport', 'nexmo');
        $this->app['config']->set('sms.providers.nexmo.key', 'example');
        $this->app['config']->set('sms.providers.nexmo.secret', 'example');

        $transport = $this->app['sms']->getTransport();
        $this->assertInstanceOf(NexmoTransport::class, $transport);

        $nexmo = $transport->nexmo();
        $this->assertInstanceOf(Client::class, $nexmo);

        $this->assertInstanceOf(ClientInterface::class, $client = $nexmo->getHttpClient());
    }
}