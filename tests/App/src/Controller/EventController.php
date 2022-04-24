<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\BarCreated;
use App\Event\FooCreated;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('message')]
final class EventController
{
    #[Route('/foo', methods: Request::METHOD_POST)]
    public function createFoo(MessageDispatcher $defaultDispatcher): JsonResponse
    {
        $event = new FooCreated('foo');
        $message = new Message($event, ['__first_header' => 'first_value', '__second_header' => 'second_value']);
        $defaultDispatcher->dispatch($message);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }

    #[Route('/bar', methods: Request::METHOD_POST)]
    public function createBar(MessageDispatcher $defaultDispatcher): JsonResponse
    {
        $event = new BarCreated('bar');
        $message = new Message($event, ['__first_header' => 'first_value', '__second_header' => 'second_value']);
        $defaultDispatcher->dispatch($message);

        return new JsonResponse('ok', Response::HTTP_CREATED);
    }
}
