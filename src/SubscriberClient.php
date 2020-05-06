<?php

namespace Jag\Subscriber\GooglePubSub;

use Google\Cloud\PubSub\PubSubClient;
use Jag\Contracts\GooglePubSub\SubscriberClient as SubscriberClientContract;

class SubscriberClient implements SubscriberClientContract
{
    /**
     * @var \Google\Cloud\PubSub\PubSubClient
     */
    protected $client;

    /**
     * @var int
     */
    protected $maxMessages;

    /**
     * @var bool
     */
    protected $isReturnImmediately;

    /**
     * SubscriberClient constructor.
     *
     * @param \Google\Cloud\PubSub\PubSubClient|\Jag\Contracts\GooglePubSub\PubSubClient $client
     * @param int                                                                        $maxMessages
     * @param bool                                                                       $isReturnImmediately
     */
    public function __construct(PubSubClient $client, int $maxMessages = 1000, bool $isReturnImmediately = false)
    {
        $this->client = $client;
        $this->maxMessages = $maxMessages;
        $this->isReturnImmediately = $isReturnImmediately;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getMaxMessages() : int
    {
        return $this->maxMessages;
    }

    public function isReturnImmediately() : bool
    {
        return $this->isReturnImmediately;
    }
}
