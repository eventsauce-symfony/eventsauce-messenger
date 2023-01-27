<?php

declare(strict_types=1);

namespace App\Event;

final readonly class DefaultCreated
{
    public function __construct(
        public string $value,
    ) {
    }
}
