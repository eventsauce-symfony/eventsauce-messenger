<?php

declare(strict_types=1);

namespace App\Event;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class FooCreated implements SerializablePayload
{
    public function __construct(
        public string $foo,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'foo' => $this->foo,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['foo'],
        );
    }
}
