<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\Middleware;

use Andreo\EventSauce\Messenger\Stamp\MessageStamp;
use EventSauce\EventSourcing\Message;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

final class HandleEventSauceMessageMiddleware implements MiddlewareInterface
{
    use LoggerAwareTrait;

    private HandlersLocatorInterface $handlersLocator;

    private bool $allowNoHandlers;

    public function __construct(HandlersLocatorInterface $handlersLocator, bool $allowNoHandlers = true)
    {
        $this->handlersLocator = $handlersLocator;
        $this->allowNoHandlers = $allowNoHandlers;
        $this->logger = new NullLogger();
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $handler = null;
        $message = $envelope->getMessage();
        assert(null !== $this->logger);

        $context = [
            'message' => $message,
            'class' => \get_class($message),
        ];

        $exceptions = [];
        foreach ($this->handlersLocator->getHandlers($envelope) as $handlerDescriptor) {
            if ($this->messageHasAlreadyBeenHandled($envelope, $handlerDescriptor)) {
                continue;
            }

            try {
                $handler = $handlerDescriptor->getHandler();

                /** @var MessageStamp[] $messageStamps */
                $messageStamps = $envelope->all(MessageStamp::class);
                $messageStamp = current($messageStamps);
                if ($messageStamp) {
                    $result = $handler(new Message($message, $messageStamp->headers));
                } else {
                    $result = $handler($message);
                }

                $handledStamp = HandledStamp::fromDescriptor($handlerDescriptor, $result);
                $envelope = $envelope->with($handledStamp);
                $this->logger->info('Message {class} handled by {handler}', $context + ['handler' => $handledStamp->getHandlerName()]);
            } catch (Throwable $e) {
                $exceptions[] = $e;
            }
        }

        if (null === $handler) {
            if (!$this->allowNoHandlers) {
                throw new NoHandlerForMessageException(sprintf('No handler for message "%s".', $context['class']));
            }

            $this->logger->info('No handler for message {class}', $context);
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
