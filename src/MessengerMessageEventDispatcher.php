<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageEventDispatcher implements MessageDispatcher
{
    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->eventBus->dispatch($message->event());
        }
    }
}
