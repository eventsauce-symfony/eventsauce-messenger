<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Tests\App\src\Event;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final readonly class BazCreated implements SerializablePayload
{
    public function __construct(
        public string $baz,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'baz' => $this->baz,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['baz'],
        );
    }
}
