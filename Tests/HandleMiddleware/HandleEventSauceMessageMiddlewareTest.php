<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Tests\HandleMiddleware;

use Andreo\EventSauce\Messenger\Middleware\HandleEventSauceMessageMiddleware;
use Andreo\EventSauce\Messenger\Stamp\EventSauceMessageStamp;
use EventSauce\EventSourcing\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

final class HandleEventSauceMessageMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function should_handle_eventsauce_message(): void
    {
        $event = new DummyEvent();
        $message = new Message($event);

        $handler = $this->createPartialMock(FakeHandler::class, ['__invoke']);

        $middleware = new HandleEventSauceMessageMiddleware(
            new HandlersLocator([
                $event::class => [$handler],
            ])
        );

        $handler
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->callback(static fn ($subject) => $subject instanceof Message && $subject->payload() === $event)
            );

        $envelope = Envelope::wrap($event, [new EventSauceMessageStamp($message)]);
        $middleware->handle($envelope, new StackMiddleware());
    }
}
