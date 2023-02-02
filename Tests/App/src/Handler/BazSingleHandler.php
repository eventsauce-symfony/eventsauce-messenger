<?php

declare(strict_types=1);

namespace App\Handler;

use Andreo\EventSauce\Messenger\Attribute\AsEventSauceMessageHandler;
use Andreo\EventSauce\Messenger\EventConsumer\InjectedHandleMethodInflector;
use App\Entity\EventStore;
use App\Event\BazCreated;
use App\Repository\EventStoreRepository;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use EventSauce\EventSourcing\Message;

final class BazSingleHandler extends EventConsumer
{
    use InjectedHandleMethodInflector;

    public function __construct(
        private readonly EventStoreRepository $repository,
        private readonly HandleMethodInflector $handleMethodInflector
    )
    {
    }

    #[AsEventSauceMessageHandler(bus: 'eventBus')]
    public function onBazCreated(BazCreated $bazCreated, Message $message): void
    {
        $eventStore = EventStore::add(
            $bazCreated,
            $message,
            'single'
        );

        $this->repository->add($eventStore);
    }
}
