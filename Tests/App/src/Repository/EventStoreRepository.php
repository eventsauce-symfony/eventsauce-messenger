<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventStore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class EventStoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventStore::class);
    }

    public function add(EventStore $eventStore): void
    {
        $this->getEntityManager()->persist($eventStore);
        $this->getEntityManager()->flush();
    }
}
