<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

#[Entity]
class EventStore
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    public int $id;

    #[Column(type: 'string')]
    public string $type;

    #[Column(type: 'text')]
    public string $event;

    #[Column(type: 'text')]
    public string $headers;

    #[Column(type: 'string')]
    public string $context;

    public static function add(Message $message, string $context): self
    {
        $new = new self();

        $event = $message->event();
        assert($event instanceof SerializablePayload);
        $new->type = $event::class;
        $new->event = json_encode($event->toPayload(), JSON_THROW_ON_ERROR);
        $new->headers = json_encode($message->headers(), JSON_THROW_ON_ERROR);
        $new->context = $context;

        return $new;
    }
}
