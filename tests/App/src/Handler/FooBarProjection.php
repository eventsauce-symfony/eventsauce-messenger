<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\EventStore;
use App\Event\BarCreated;
use App\Event\FooCreated;
use App\Repository\EventStoreRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class FooBarProjection implements MessageConsumer, MessageSubscriberInterface
{
    public function __construct(private EventStoreRepository $repository)
    {
    }

    public function handle(Message $message): void
    {
        assert($message->event() instanceof FooCreated || $message->event() instanceof BarCreated);

        $eventStore = EventStore::add($message, 'projection');

        $this->repository->add($eventStore);
    }

    public static function getHandledMessages(): iterable
    {
        yield FooCreated::class => [
            'method' => 'handle',
            'bus' => 'eventBus',
        ];
        yield BarCreated::class => [
            'method' => 'handle',
            'bus' => 'eventBus',
        ];
    }
}
