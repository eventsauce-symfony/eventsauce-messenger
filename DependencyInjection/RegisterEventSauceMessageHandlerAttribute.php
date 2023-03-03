<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\DependencyInjection;

use Andreo\EventSauce\Messenger\Attribute\AsEventSauceMessageHandler;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Reflector;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class RegisterEventSauceMessageHandlerAttribute
{
    public static function register(ContainerBuilder $container, string $handleMethod = 'handle'): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsEventSauceMessageHandler::class,
            static function (ChildDefinition $definition, AsEventSauceMessageHandler $attribute, Reflector $reflector) use ($handleMethod): void {
                assert($reflector instanceof ReflectionMethod);

                $eventParameter = $reflector->getParameters()[0] ?? null;
                if (null === $eventParameter) {
                    return;
                }

                $tagAttributes['method'] = $handleMethod;
                $tagAttributes['from_transport'] = $attribute->fromTransport;
                $tagAttributes['bus'] = $attribute->bus;
                $tagAttributes['priority'] = $attribute->priority;

                $handles = is_string($attribute->handles) ? [$attribute->handles] : $attribute->handles;
                assert(is_array($handles));

                if (empty($handles)) {
                    $eventParameterType = $eventParameter->getType();
                    $eventParameterTypesToRegister = [];
                    if ($eventParameterType instanceof ReflectionNamedType) {
                        $eventParameterTypesToRegister = [$eventParameterType];
                    } elseif ($eventParameterType instanceof ReflectionUnionType) {
                        $eventParameterTypesToRegister = $eventParameterType->getTypes();
                    }

                    foreach ($eventParameterTypesToRegister as $eventParameterTypeToRegister) {
                        $handles[] = $eventParameterTypeToRegister->getName();
                    }
                }

                foreach ($handles as $handle) {
                    $tagAttributes['handles'] = $handle;
                    $definition->addTag('messenger.message_handler', $tagAttributes);
                }
            }
        );
    }
}
