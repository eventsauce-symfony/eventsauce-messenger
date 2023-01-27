<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Dispatcher;

use Andreo\EventSauce\Messenger\Stamp\EventSauceMessageStamp;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerMessageDispatcher implements MessageDispatcher
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->eventBus->dispatch(
                Envelope::wrap($message->payload(), [
                    new EventSauceMessageStamp($message),
                ])
            );
        }
    }
}
