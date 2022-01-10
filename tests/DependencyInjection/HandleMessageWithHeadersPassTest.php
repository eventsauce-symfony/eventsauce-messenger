<?php

declare(strict_types=1);


namespace DependencyInjection;

use Andreo\EventSauce\Messenger\DependencyInjection\HandleMessageWithHeadersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Tests\DependencyInjection\FakeHandler;

final class HandleMessageWithHeadersPassTest extends TestCase
{
    private ContainerBuilder $container;

    private string $busId = 'foo_bus';

    /**
     * @test
     */
    public function handle_message_middleware_decorated(): void
    {
        $compiler = new HandleMessageWithHeadersPass();
        $compiler->process($this->container);

        $has = $this->container->has("{$this->busId}.middleware.handle_message_with_headers");
        $this->assertTrue($has);
    }

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->register( $this->busId, MessageBus::class);
        $this->container->register(FakeHandler::class, FakeHandler::class)
            ->addTag('andreo.event_sauce.messenger_dispatcher_with_headers', [
                'bus' => $this->busId,
            ]);

        $this->container
            ->register("{$this->busId}.middleware.handle_message", HandleMessageMiddleware::class)
            ->addArgument(new Definition(HandlersLocator::class));
    }
}