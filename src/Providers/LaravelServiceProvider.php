<?php

namespace Jag\Subscriber\GooglePubSub\Providers;

use Illuminate\Support\Str;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Jag\Subscriber\GooglePubSub\Dispatcher;
use Jag\Subscriber\GooglePubSub\SubscriberClient;
use Jag\Exceptions\GooglePubSub\KeyNotFoundException;
use Jag\Subscriber\GooglePubSub\Console\PubSubSubscribeConsole;
use Jag\Contracts\GooglePubSub\Dispatcher as DispatcherContract;
use Jag\Contracts\GooglePubSub\PubSubClient as PubSubClientContract;
use Jag\Contracts\GooglePubSub\SubscriberClient as SubscriberClientContract;

class LaravelServiceProvider extends ServiceProvider
{
    protected const CONFIG_KEY = 'subscriber.connections.google';

    public function register() : void
    {
        $this->mergeConfigFrom(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . implode(
            DIRECTORY_SEPARATOR,
            ['config', 'google.php']
        ), self::CONFIG_KEY);
        $this->bindPubSubClient();
        $this->bindSubscribeCommand();
        $this->bindSubscriberClient();
        $this->bindDispatcher();
    }

    protected function bindDispatcher() : void
    {
        $this->app->singleton(DispatcherContract::class, function ($app) {
            return new Dispatcher($app);
        });
        $this->app->bind('google-pubsub.subscriber.dispatcher', DispatcherContract::class);
    }

    protected function bindPubSubClient() : void
    {
        $this->app->singleton(PubSubClientContract::class, function ($app) {
            return new PubSubClient($this->createClientConfig($app->make('config')));
        });
        $this->app->bind('google-pubsub.subscriber.client', PubSubClientContract::class);
    }

    protected function createClientConfig(Repository $config)
    {
        if (!empty($config->get(self::CONFIG_KEY . '.override_config', []))) {
            return array_merge(
                $config->get(self::CONFIG_KEY . '.override_config', []),
                [
                    'projectId' => $config->get(self::CONFIG_KEY . '.project_id'),
                ]
            );
        }

        return [
            'projectId' => $config->get(self::CONFIG_KEY . '.project_id'),
            'keyFilePath' => $this->getKeyContent($config->get(self::CONFIG_KEY . '.credentials_path')),
        ];
    }

    /**
     * @param string|null $path
     *
     * @throws \Jag\Exceptions\GooglePubSub\KeyNotFoundException
     * @return string
     */
    protected function getKeyContent($path = null) : string
    {
        if ($path === null) {
            return $this->getKeyContent(storage_path('key.json'));
        }
        if (Str::startsWith($path, 'storage')) {
            return $this->getKeyContent(storage_path(substr($path, 8)));
        }
        if (!file_exists($path)) {
            throw new KeyNotFoundException($path);
        }

        return $path;
    }

    protected function bindSubscriberClient() : void
    {
        $this->app->singleton(SubscriberClientContract::class, function ($app) {
            /** @var Repository $config */
            $config = $app->make('config');

            return new SubscriberClient(
                $app->make('google-pubsub.subscriber.client'),
                $config->get(self::CONFIG_KEY . '.max_messages'),
                $config->get(self::CONFIG_KEY . '.return_immediately')
            );
        });
        $this->app->bind('google-pubsub.subscriber.subscriber_client', SubscriberClientContract::class);
    }

    protected function bindSubscribeCommand() : void
    {
        $this->app->singleton('google-pubsub.subscriber.subscriber_command', function () {
            return new PubSubSubscribeConsole();
        });
        $this->commands([
            'google-pubsub.subscriber.subscriber_command'
        ]);
    }
}
