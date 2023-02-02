<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\BarCreated;
use App\Event\BazCreated;
use App\Event\DefaultCreated;
use App\Event\FooCreated;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('message')]
final class EventStoreController
{
    #[Route('/foo', methods: Request::METHOD_POST)]
    public function createFoo(MessageDispatcher $messageDispatcher): JsonResponse
    {
        $event = new FooCreated('foo');
        $message = new Message(
            $event,
            [
                '__first_header' => 'foo_value',
                '__second_header' => 'foo1_value',
            ]
        );
        $messageDispatcher->dispatch($message);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }

    #[Route('/bar', methods: Request::METHOD_POST)]
    public function createBar(MessageDispatcher $messageDispatcher): JsonResponse
    {
        $event = new BarCreated('bar');
        $message = new Message(
            $event,
            [
                '__first_header' => 'bar_value',
                '__second_header' => 'bar2_value',
            ]
        );

        $messageDispatcher->dispatch($message);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }

    #[Route('/baz', methods: Request::METHOD_POST)]
    public function createBaz(MessageDispatcher $messageDispatcher): JsonResponse
    {
        $event = new BazCreated('baz');
        $message = new Message(
            $event,
            [
                '__first_header' => 'baz_value',
                '__second_header' => 'baz2_value',
            ]
        );

        $messageDispatcher->dispatch($message);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }

    #[Route('/default', methods: Request::METHOD_POST)]
    public function createDefault(MessageBusInterface $messageBus): JsonResponse
    {
        $event = new DefaultCreated('main');

        $messageBus->dispatch($event);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }
}
