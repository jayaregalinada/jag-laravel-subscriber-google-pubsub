<?php

namespace Tests;

use Mockery as m;
use Google\Cloud\PubSub\PubSubClient;
use Jag\Subscriber\GooglePubSub\SubscriberClient;

class SubscriberClientTest extends TestCase
{
    public function testPubSubClient()
    {
        $pubSubClient = m::mock(PubSubClient::class);
        $client = new SubscriberClient($pubSubClient);
        $this->assertSame($pubSubClient, $client->getClient());
    }

    public function testMaxMessages()
    {
        $mock = m::mock(PubSubClient::class);
        $client = new SubscriberClient($mock, 2000);
        $this->assertEquals(2000, $client->getMaxMessages());
    }

    public function testIsReturnImmediately()
    {
        $mock = m::mock(PubSubClient::class);
        $client = new SubscriberClient($mock, 1000, true);
        $this->assertTrue($client->isReturnImmediately());

        $client = new SubscriberClient($mock, 1000);
        $this->assertFalse($client->isReturnImmediately());
    }
}
