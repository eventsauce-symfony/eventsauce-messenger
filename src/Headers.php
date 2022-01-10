<?php

declare(strict_types=1);


namespace Andreo\EventSauce\Messenger;

use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\Header;

final class Headers
{
    /**
     * @param array<string, int|string|array<mixed>|AggregateRootId|bool|float> $headers
     */
    private function __construct(private array $headers) {}

    public function aggregateVersion(): int
    {
        /** @var int|string $version */
        $version = $this->headers[Header::AGGREGATE_ROOT_VERSION];

        return (int)$version;
    }

    public function aggregateRootId(): AggregateRootId
    {
        /** @var AggregateRootId $id */
        $id = $this->headers[Header::AGGREGATE_ROOT_ID];

        return $id;
    }

    public function aggregateRootType(): string
    {
        /** @var string $type */
        $type = $this->headers[Header::AGGREGATE_ROOT_TYPE];

        return $type;
    }

    public function exists(string $header): bool
    {
        return null !== $this->header($header);
    }

    /**
     * @return int|string|array<mixed>|AggregateRootId|null|bool|float
     */
    public function header(string $key): int|string|array|AggregateRootId|null|bool|float
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * @param array<string, int|string|array<mixed>|AggregateRootId|bool|float> $headers
     */
    public static function create(array $headers): self
    {
        return new self($headers);
    }
}