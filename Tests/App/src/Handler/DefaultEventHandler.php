<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\EventStore;
use App\Event\DefaultCreated;
use App\Repository\EventStoreRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DefaultEventHandler
{
    public function __construct(private EventStoreRepository $repository)
    {
    }

    public function __invoke(DefaultCreated $fooCreated): void
    {
        $eventStore = EventStore::addDefault($fooCreated);

        $this->repository->add($eventStore);
    }
}
