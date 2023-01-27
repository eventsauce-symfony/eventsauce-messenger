<?php

declare(strict_types=1);

namespace App\Handler;

use Andreo\EventSauce\Messenger\Attribute\AsEventSauceMessageHandler;
use Andreo\EventSauce\Messenger\EventConsumer\InjectedHandleMethodInflector;
use App\Entity\EventStore;
use App\Event\BarCreated;
use App\Event\FooCreated;
use App\Repository\EventStoreRepository;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;

final class FooBarProjection extends EventConsumer
{
    use InjectedHandleMethodInflector;

    public function __construct(
        private readonly EventStoreRepository $repository,
        private readonly HandleMethodInflector $handleMethodInflector
    )
    {
    }

    #[AsEventSauceMessageHandler(bus: 'eventBus')]
    public function onFooCreated(FooCreated $fooCreated, Message $message): void
    {
        $eventStore = EventStore::add(
            $fooCreated,
            $message,
            'projection'
        );

        $this->repository->add($eventStore);
    }

    #[AsEventSauceMessageHandler(bus: 'eventBus')]
    public function onBarCreated(BarCreated $barCreated, Message $message): void
    {
        $eventStore = EventStore::add(
            $barCreated,
            $message,
            'projection'
        );

        $this->repository->add($eventStore);
    }
}
