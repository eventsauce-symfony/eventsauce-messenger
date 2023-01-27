<?php

declare(strict_types=1);

namespace App\Event;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class BarCreated implements SerializablePayload
{
    public function __construct(
        public string $bar,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'bar' => $this->bar,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['bar'],
        );
    }
}
