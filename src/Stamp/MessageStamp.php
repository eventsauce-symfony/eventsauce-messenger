<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class MessageStamp implements StampInterface
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(public array $headers)
    {
    }
}
