<?php

declare(strict_types=1);

namespace Tests\MessageDispatcher;

use Andreo\EventSauce\Messenger\MessengerMessageDispatcher;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function message_dispatched(): void
    {
        $testMessage = new Message(new stdClass());
        $testEnvelope = Envelope::wrap($testMessage);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($testMessage)
            ->willReturn($testEnvelope)
        ;

        $dispatcher = new MessengerMessageDispatcher($eventBusMock);
        $dispatcher->dispatch($testMessage);
    }
}
