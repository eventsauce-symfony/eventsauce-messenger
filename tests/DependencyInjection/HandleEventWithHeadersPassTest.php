<?php

declare(strict_types=1);

namespace DependencyInjection;

use Andreo\EventSauce\Messenger\DependencyInjection\HandleEventWithHeadersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Tests\DependencyInjection\DummyHandler;

final class HandleEventWithHeadersPassTest extends TestCase
{
    private ContainerBuilder $container;

    private string $busId = 'foo_bus';

    /**
     * @test
     */
    public function handle_message_middleware_decorated(): void
    {
        $compiler = new HandleEventWithHeadersPass();
        $compiler->process($this->container);

        $has = $this->container->has("{$this->busId}.middleware.handle_event_with_headers");
        $this->assertTrue($has);
    }

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->register($this->busId, MessageBus::class);
        $this->container->register(DummyHandler::class, DummyHandler::class)
            ->addTag('andreo.event_sauce.event_with_headers_dispatcher', [
                'bus' => $this->busId,
            ]);

        $this->container
            ->register("{$this->busId}.middleware.handle_message", HandleMessageMiddleware::class)
            ->addArgument(new Definition(HandlersLocator::class));
    }
}
