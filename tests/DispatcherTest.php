<?php

namespace Tests;

use stdClass;
use Mockery as m;
use Google\Cloud\PubSub\Message;
use Illuminate\Container\Container;
use Jag\Subscriber\GooglePubSub\Dispatcher;

class DispatcherTest extends TestCase
{
    public function testSubscriber()
    {
        $d = new Dispatcher();
        $d->listen('foo', function ($payload) {
            return $payload;
        });
        $p = m::mock(Message::class);
        $response = $d->dispatch('foo', $p);

        $this->assertIsArray($response);
        $this->assertContains($p, $response);
    }

    public function testReturningFalsyValues()
    {
        $m = m::mock(Message::class);
        $d = new Dispatcher();
        $d->listen('foo', function () {
            return 0;
        });
        $d->listen('foo', function () {
            return [];
        });
        $d->listen('foo', function () {
            return '';
        });
        $d->listen('foo', function () {
        });
        $res = $d->dispatch('foo', $m);
        $this->assertEquals([0, [], '', null], $res);
    }

    public function testWithContainerResolution()
    {
        $d = new Dispatcher($c = m::mock(Container::class));
        $m = m::mock(Message::class);
        $c->shouldReceive('make')->once()->with('FooHandler')->andReturn($h = m::mock(stdClass::class));
        $h->shouldReceive('handle')->once()->with($m)->andReturn('baz');
        $d->listen('foo', 'FooHandler');
        $res = $d->dispatch('foo', $m);

        $this->assertEquals(['baz'], $res);
    }
}
