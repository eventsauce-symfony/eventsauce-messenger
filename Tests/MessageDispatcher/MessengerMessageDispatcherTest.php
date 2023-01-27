<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Tests\MessageDispatcher;

use Andreo\EventSauce\Messenger\Dispatcher\MessengerMessageDispatcher;
use Andreo\EventSauce\Messenger\Stamp\EventSauceMessageStamp;
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
    public function should_dispatch_event_sauce_message(): void
    {
        $event = new stdClass();
        $message = new Message($event);
        $envelope = Envelope::wrap($event, [new EventSauceMessageStamp($message)]);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($envelope)
            ->willReturn($envelope)
        ;

        $dispatcher = new MessengerMessageDispatcher($eventBusMock);
        $dispatcher->dispatch($message);
    }
}
