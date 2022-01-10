<?php

declare(strict_types=1);


namespace Andreo\EventSauce\Messenger;


use Andreo\EventSauce\Messenger\Stamp\HeadersStamp;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageEventWithHeadersDispatcher implements MessageDispatcher
{
    public function __construct(private MessageBusInterface $eventBus) {}

    public function dispatch(Message ...$messages): void
    {
        foreach ($messages as $message) {
            $this->eventBus->dispatch(
                Envelope::wrap($message->event(), [
                    new HeadersStamp(Headers::create($message->headers())),
                ])
            );
        }
    }
}
