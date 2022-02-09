<?php

declare(strict_types=1);

namespace Tests\EventAndHeadersDispatcher;

use Andreo\EventSauce\Messenger\Headers;
use Andreo\EventSauce\Messenger\MessengerEventAndHeadersDispatcher;
use Andreo\EventSauce\Messenger\Stamp\HeadersStamp;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class EventAndHeadersDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function should_dispatch_event_and_headers(): void
    {
        $event = new stdClass();
        $headers = ['_foo' => 'foo'];
        $testEnvelope = Envelope::wrap($event, [new HeadersStamp(Headers::create($headers))]);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($testEnvelope)
            ->willReturn($testEnvelope)
        ;

        $dispatcher = new MessengerEventAndHeadersDispatcher($eventBusMock);
        $dispatcher->dispatch(new Message($event, $headers));
    }
}
