<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\DependencyInjection;

use Andreo\EventSauce\Messenger\Middleware\HandleEventWithHeadersMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandleEventWithHeadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $messengerDispatcherIds = $container->findTaggedServiceIds('andreo.event_sauce.event_with_headers_dispatcher');
        foreach ($messengerDispatcherIds as $attrs) {
            $busId = $attrs[0]['bus'];
            if (!$container->has($busId)) {
                continue;
            }

            if (!$container->has($defaultHandleMessageMiddlewareId = "$busId.middleware.handle_message")) {
                continue;
            }

            $defaultHandleMessageMiddlewareDef = $container->getDefinition($defaultHandleMessageMiddlewareId);
            $container
                ->register("$busId.middleware.handle_event_with_headers", HandleEventWithHeadersMiddleware::class)
                ->addArgument($defaultHandleMessageMiddlewareDef->getArgument(0))
                ->setDecoratedService($defaultHandleMessageMiddlewareId)
                ->addMethodCall('setLogger', [new Reference('monolog.logger.messenger')])
            ;
        }
    }
}
