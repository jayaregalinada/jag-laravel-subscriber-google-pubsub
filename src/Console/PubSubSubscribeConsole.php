<?php

namespace Jag\Subscriber\GooglePubSub\Console;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Google\Cloud\PubSub\Message;
use Jag\Contracts\GooglePubSub\Dispatcher;
use Jag\Contracts\GooglePubSub\SubscriberClient;

class PubSubSubscribeConsole extends Command
{
    protected $signature = 'pubsub:subscribe
                            {subscription : The Subscription you want to listen}';

    protected $description = 'Subscriber of Google PubSub';

    public function handle(SubscriberClient $client, Dispatcher $dispatcher) : void
    {
        $subscription = $client->getClient()->subscription($this->getSubscriptionName());
        $loopActive = true;
        while ($loopActive) {
            $messages = $subscription->pull([
                'returnImmediately' => $client->isReturnImmediately(),
                'maxMessages' => $client->getMaxMessages(),
            ]);
            if (empty($messages)) {
                continue;
            }
            foreach ($messages as $message) {
                $this->writeOutput('starting', $this->getSubscriptionName(), $message);
                $dispatcher->dispatch($this->getSubscriptionName(), $message);
                $subscription->acknowledge($message);
                $this->writeOutput('success', $this->getSubscriptionName(), $message);
            }
        }
    }

    protected function getSubscriptionName()
    {
        return $this->argument('subscription');
    }

    /**
     * @param string                       $type
     * @param string                       $subscription
     * @param \Google\Cloud\PubSub\Message $message
     * @return void
     */
    protected function writeOutput(string $type, string $subscription, Message $message) : void
    {
        switch ($type) {
            case 'starting':
                $this->writeStatus($subscription, $message, 'comment');
                break;
            case 'success':
                $this->writeStatus($subscription, $message, 'info');
                break;
            case 'failed':
                $this->writeStatus($subscription, $message, 'error');
                break;
        }
    }

    /**
     * @param string                       $subscription
     * @param \Google\Cloud\PubSub\Message $message
     * @param string                       $type
     * @return void
     */
    protected function writeStatus(string $subscription, Message $message, string $type) : void
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s",
            Carbon::now()->format('Y-m-d H:i:s'),
            $subscription,
            $message->id(),
            $message->ackId()
        ));
    }
}
