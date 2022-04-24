<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\EventStore;
use App\Event\FooCreated;
use App\Repository\EventStoreRepository;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'eventBus', handles: FooCreated::class, method: 'handle')]
final class FooCreatedHandler implements MessageConsumer
{
    public function __construct(private EventStoreRepository $repository)
    {
    }

    public function handle(Message $message): void
    {
        assert($message->event() instanceof FooCreated);

        $eventStore = EventStore::add($message, 'handler');

        $this->repository->add($eventStore);
    }
}
