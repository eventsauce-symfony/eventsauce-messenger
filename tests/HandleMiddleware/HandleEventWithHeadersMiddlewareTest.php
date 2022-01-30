<?php

declare(strict_types=1);

namespace Tests\HandleMiddleware;

use Andreo\EventSauce\Messenger\Headers;
use Andreo\EventSauce\Messenger\Middleware\HandleEventWithHeadersMiddleware;
use Andreo\EventSauce\Messenger\Stamp\HeadersStamp;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

final class HandleEventWithHeadersMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function event_with_headers_handling(): void
    {
        $message = new DummyMessage();
        $headers = Headers::create(['_foo' => 'foo']);

        $handler = $this->createPartialMock(FakeHandler::class, ['__invoke']);

        $middleware = new HandleEventWithHeadersMiddleware(
            new HandlersLocator([
                $message::class => [$handler],
            ])
        );

        $handler->expects($this->once())->method('__invoke')->with($message, $headers);

        $envelope = Envelope::wrap($message, [new HeadersStamp($headers)]);
        $middleware->handle($envelope, new StackMiddleware());
    }
}
