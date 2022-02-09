<?php

declare(strict_types=1);

namespace DependencyInjection;

use Andreo\EventSauce\Messenger\DependencyInjection\HandleEventAndHeadersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Tests\DependencyInjection\DummyMessageDispatcher;

final class HandleEventAndHeadersPassTest extends TestCase
{
    private ContainerBuilder $container;

    private string $busId = 'foo_bus';

    /**
     * @test
     */
    public function should_register_handle_event_and_headers_middleware_as_decorator(): void
    {
        $compiler = new HandleEventAndHeadersPass();
        $compiler->process($this->container);

        $has = $this->container->has("{$this->busId}.middleware.handle_event_and_headers");
        $this->assertTrue($has);
        $definition = $this->container->getDefinition("{$this->busId}.middleware.handle_event_and_headers");
        $this->assertContains("{$this->busId}.middleware.handle_message", $definition->getDecoratedService());
    }

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->register($this->busId, MessageBus::class);
        $this->container->register(DummyMessageDispatcher::class, DummyMessageDispatcher::class)
            ->addTag('andreo.event_sauce.event_and_headers_dispatcher', [
                'bus' => $this->busId,
            ]);

        $this->container
            ->register("{$this->busId}.middleware.handle_message", HandleMessageMiddleware::class)
            ->addArgument(new Definition(HandlersLocator::class));
    }
}
