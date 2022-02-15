<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\DependencyInjection;

use Andreo\EventSauce\Messenger\Middleware\HandleEventAndHeadersMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandleEventAndHeadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $messengerDispatcherIds = $container->findTaggedServiceIds('andreo.event_sauce.event_and_headers_dispatcher');
        foreach ($messengerDispatcherIds as $attrs) {
            $busId = $attrs[0]['bus'];
            if (!$container->has($busId)) {
                continue;
            }

            if (!$container->has($defaultHandleMessageMiddlewareId = "$busId.middleware.handle_message")) {
                continue;
            }

            $originHandleMessageMiddlewareDef = $container->getDefinition($defaultHandleMessageMiddlewareId);
            $handleMessageMiddlewareDef = $container
                ->register("$busId.middleware.handle_event_and_headers", HandleEventAndHeadersMiddleware::class)
                ->addArgument($originHandleMessageMiddlewareDef->getArgument(0))
                ->setDecoratedService($defaultHandleMessageMiddlewareId)
            ;
            if ($container->has('monolog.logger.messenger')) {
                $handleMessageMiddlewareDef
                    ->addMethodCall('setLogger', [new Reference('monolog.logger.messenger')]);
            }
        }
    }
}
