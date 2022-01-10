<?php

declare(strict_types=1);


namespace EventDispatcher;

use Andreo\EventSauce\Messenger\MessengerMessageEventDispatcher;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageEventDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function message_dispatched(): void
    {
        $event = new stdClass();
        $testEnvelope = Envelope::wrap($event);

        $eventBusMock = $this->createConfiguredMock(MessageBusInterface::class, [
            'dispatch' => $testEnvelope,
        ]);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn($testEnvelope)
        ;

        $dispatcher = new MessengerMessageEventDispatcher($eventBusMock);
        $dispatcher->dispatch(new Message($event));
    }
}