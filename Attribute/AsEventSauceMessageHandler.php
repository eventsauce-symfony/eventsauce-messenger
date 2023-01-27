<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class AsEventSauceMessageHandler
{
    public function __construct(
        public ?string $bus = null,
        public ?string $fromTransport = null,
        public int $priority = 0,
    ) {
    }
}
