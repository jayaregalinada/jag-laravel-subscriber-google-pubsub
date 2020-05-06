<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Jag\Subscriber\GooglePubSub\Providers\LaravelServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function getPackageProviders($app) : array
    {
        return [
            LaravelServiceProvider::class,
        ];
    }
}
