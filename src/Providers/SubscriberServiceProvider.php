<?php

namespace Jag\Subscriber\GooglePubSub\Providers;

use Illuminate\Support\ServiceProvider;
use Jag\Contracts\GooglePubSub\Dispatcher;

class SubscriberServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot() : void
    {
        /** @var \Jag\Contracts\GooglePubSub\Dispatcher $dispatcher */
        $dispatcher = $this->app->make(Dispatcher::class);

        foreach ($this->listen as $subscription => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $dispatcher->listen($subscription, $listener);
            }
        }
    }
}
