<?php

declare(strict_types=1);

namespace Tests\MessageDispatcher;

use Andreo\EventSauce\Messenger\MessengerMessageDispatcher;
use Andreo\EventSauce\Messenger\Stamp\MessageStamp;
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
    public function should_dispatch_event_and_headers(): void
    {
        $event = new stdClass();
        $headers = ['_foo' => 'foo'];
        $envelope = Envelope::wrap($event, [new MessageStamp($headers)]);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($envelope)
            ->willReturn($envelope)
        ;

        $dispatcher = new MessengerMessageDispatcher($eventBusMock);
        $dispatcher->dispatch(new Message($event, $headers));
    }
}
