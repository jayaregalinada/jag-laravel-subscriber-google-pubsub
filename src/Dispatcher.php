<?php

namespace Jag\Subscriber\GooglePubSub;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Google\Cloud\PubSub\Message;
use Illuminate\Container\Container;
use Jag\Contracts\GooglePubSub\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Container\Container as ContainerContract;

class Dispatcher implements DispatcherContract
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    public function __construct(ContainerContract $container = null)
    {
        $this->container = $container ?: new Container();
    }

    /**
     * @param string          $subscription
     * @param \Closure|string $listener
     */
    public function listen(string $subscription, $listener) : void
    {
        $this->listeners[$subscription][] = $this->makeListener($listener);
    }

    protected function makeListener($listener) : callable
    {
        if (is_string($listener)) {
            return $this->createListenerClass($listener);
        }

//        return function ($payload) use ($listener) {
//            return $listener();
//        };
        return function ($payload) use ($listener) {
            return $listener(...array_values($payload));
        };
    }

    protected function createListenerClass(string $listener) : Closure
    {
        return function ($payload) use ($listener) {
            return call_user_func_array($this->createCallableClass($listener), $payload);
        };
    }

    protected function createCallableClass(string $listener) : array
    {
        [$class, $method] = $this->parseHandler($listener);

        return [$this->container->make($class), $method];
    }

    protected function parseHandler(string $listener) : array
    {
        return Str::parseCallback($listener, 'handle');
    }

    /**
     * @inheritDoc
     */
    public function dispatch(string $subscription, Message $payload, $halt = false)
    {
        [$subscription, $payload] = $this->parseSubscriptionAndPayload($subscription, $payload);
        $responses = [];
        foreach ($this->getListeners($subscription) as $listener) {
            $response = $listener($payload);
            if ($halt && $response !== null) {
                return $response;
            }
            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        return $halt ? null : $responses;
    }

    public function getListeners(string $subscription) : array
    {
        return $this->listeners[$subscription] ?? [];
    }

    protected function parseSubscriptionAndPayload($subscription, Message $payload) : array
    {
        return [$subscription, Arr::wrap($payload)];
    }
}
