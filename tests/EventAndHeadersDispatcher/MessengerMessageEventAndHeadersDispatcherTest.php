<?php

declare(strict_types=1);

namespace Tests\EventAndHeadersDispatcher;

use Andreo\EventSauce\Messenger\Headers;
use Andreo\EventSauce\Messenger\MessengerMessageEventAndHeadersDispatcher;
use Andreo\EventSauce\Messenger\Stamp\HeadersStamp;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerMessageEventAndHeadersDispatcherTest extends TestCase
{
    /**
     * @test
     */
    public function message_dispatched(): void
    {
        $event = new stdClass();
        $headers = ['_foo' => 'foo'];
        $testEnvelope = Envelope::wrap($event, [new HeadersStamp(Headers::create($headers))]);

        $eventBusMock = $this->createMock(MessageBusInterface::class);
        $eventBusMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(Envelope::class),
            )
            ->willReturn($testEnvelope)
        ;

        $dispatcher = new MessengerMessageEventAndHeadersDispatcher($eventBusMock);
        $dispatcher->dispatch(new Message($event, $headers));
    }
}
