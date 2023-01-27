<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Stamp;

use EventSauce\EventSourcing\Message;
use Symfony\Component\Messenger\Stamp\StampInterface;

final readonly class EventSauceMessageStamp implements StampInterface
{
    public function __construct(public Message $message)
    {
    }
}
