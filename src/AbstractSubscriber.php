<?php

namespace Jag\Subscriber\GooglePubSub;

use Google\Cloud\PubSub\Message;

abstract class AbstractSubscriber
{
    abstract public function handle(Message $message);
}
