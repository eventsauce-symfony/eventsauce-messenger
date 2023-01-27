<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Middleware;

use Andreo\EventSauce\Messenger\Stamp\EventSauceMessageStamp;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

final class HandleEventSauceMessageMiddleware implements MiddlewareInterface
{
    use LoggerAwareTrait;

    public function __construct(private readonly HandlersLocatorInterface $handlersLocator)
    {
        $this->logger = new NullLogger();
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $context = [
            'class' => $message::class,
        ];

        $exceptions = [];
        foreach ($this->handlersLocator->getHandlers($envelope) as $handlerDescriptor) {
            if ($this->messageHasAlreadyBeenHandled($envelope, $handlerDescriptor)) {
                continue;
            }

            try {
                /** @var EventSauceMessageStamp|null $eventSauceMessageStamp */
                $eventSauceMessageStamp = $envelope->last(EventSauceMessageStamp::class);
                if (null === $eventSauceMessageStamp) {
                    continue;
                }

                $handler = $handlerDescriptor->getHandler();
                $result = $handler($eventSauceMessageStamp->message);
                $handledStamp = HandledStamp::fromDescriptor($handlerDescriptor, $result);
                $envelope = $envelope->with($handledStamp);
                $this->logger?->info('EventSauce message with event {class} handled by {handler}', $context + ['handler' => $handledStamp->getHandlerName()]);
            } catch (Throwable $e) {
                $exceptions[] = $e;
            }
        }

        if (\count($exceptions)) {
            throw new HandlerFailedException($envelope, $exceptions);
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function messageHasAlreadyBeenHandled(Envelope $envelope, HandlerDescriptor $handlerDescriptor): bool
    {
        /** @var HandledStamp $stamp */
        foreach ($envelope->all(HandledStamp::class) as $stamp) {
            if ($stamp->getHandlerName() === $handlerDescriptor->getName()) {
                return true;
            }
        }

        return false;
    }
}
