<?php

declare(strict_types=1);

namespace EventDispatcher;

use Andreo\EventSauce\Messenger\MessengerEventDispatcher;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerEventDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function should_dispatch_only_event(): void
    {
        $event = new stdClass();
        $testEnvelope = Envelope::wrap($event);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn($testEnvelope)
        ;

        $dispatcher = new MessengerEventDispatcher($eventBusMock);
        $dispatcher->dispatch(new Message($event));
    }
}
