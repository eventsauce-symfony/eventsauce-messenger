<?php

declare(strict_types=1);

namespace Tests\DependencyInjection;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;

class DummyMessageDispatcher implements MessageDispatcher
{
    public function dispatch(Message ...$messages): void
    {
    }
}
