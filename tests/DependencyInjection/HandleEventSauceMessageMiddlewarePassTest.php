<?php

declare(strict_types=1);

namespace DependencyInjection;

use Andreo\EventSauce\Messenger\DependencyInjection\HandleEventSauceMessageMiddlewarePass;
use Andreo\EventSauce\Messenger\Middleware\HandleEventSauceMessageMiddleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Tests\DependencyInjection\DummyMessageDispatcher;

final class HandleEventSauceMessageMiddlewarePassTest extends TestCase
{
    private ContainerBuilder $container;

    private string $busId = 'fooBus';

    /**
     * @test
     */
    public function should_register_eventsauce_handle_message_middleware(): void
    {
        $compiler = new HandleEventSauceMessageMiddlewarePass();
        $compiler->process($this->container);

        $has = $this->container->has("{$this->busId}.middleware.handle_message");
        $this->assertTrue($has);
        $definition = $this->container->getDefinition("{$this->busId}.middleware.handle_message");
        $this->assertEquals(HandleEventSauceMessageMiddleware::class, $definition->getClass());
    }

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->register($this->busId, MessageBus::class);
        $this->container->register(DummyMessageDispatcher::class, DummyMessageDispatcher::class)
            ->addTag('andreo.eventsauce.messenger.message_dispatcher', [
                'bus' => $this->busId,
            ]);

        $this->container
            ->register("{$this->busId}.middleware.handle_message", HandleMessageMiddleware::class)
            ->addArgument(new Definition(HandlersLocator::class));
    }
}
