<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\DependencyInjection;

use Andreo\EventSauce\Messenger\Middleware\HandleEventSauceMessageMiddleware;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandleEventSauceMessageMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('andreo.eventsauce.messenger.message_dispatcher') as [$attrs]) {
            $busId = $attrs['bus'];
            if (!$container->has($busId)) {
                continue;
            }

            if (!$container->has($handleMessageMiddlewareId = "$busId.middleware.handle_message")) {
                continue;
            }

            $defaultHandleMessageMiddlewareDef = $container->getDefinition($handleMessageMiddlewareId);
            $handleMessageMiddlewareDef = $container
                ->register($handleMessageMiddlewareId, HandleEventSauceMessageMiddleware::class)
                ->addArgument($defaultHandleMessageMiddlewareDef->getArgument(0))
            ;

            if ($container->has($loggerId = 'monolog.logger.messenger')) {
                $handleMessageMiddlewareDef->addMethodCall('setLogger', [new Reference($loggerId)]);
            }
        }
    }
}
