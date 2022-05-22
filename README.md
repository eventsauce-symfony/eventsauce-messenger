## eventsauce-messenger

Integration symfony messenger for EventSauce

### Installation

```bash
composer require andreo/eventsauce-messenger
```

### Requirements

- PHP ^8.1
- Symfony messenger ^6.0

### Usage

```php
use Andreo\EventSauce\Messenger\MessengerMessageDispatcher;

new MessengerMessageDispatcher(
    eventBus: $eventBus // Symfony\Component\Messenger\MessageBusInterface
);
```

**Projection Example**

```php
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;

final class FooProjector implements MessageConsumer, MessageSubscriberInterface
{
    public function handle(Message $message)
    {
       $event = $message->payload();
        if ($event instanceof FooCreated) {
            // do something
        } 
    }
    
    public static function getHandledMessages(): iterable
    {
        yield FooCreated::class => [
            'method' => 'handle',
            'bus' => 'eventBus',
        ];
    }
}
```

**Handler Example**

```php
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(
    bus: 'eventBus',
    handles: FooCreated::class,
    method: 'handle'
)]
final class FooEventHandler implements MessageConsumer
{
    public function handle(Message $message)
    {
       $event = $message->payload();
       assert($event instanceof FooCreated);
       
       // do something
    }
}
```

### Register

```yaml
    Andreo\EventSauce\Messenger\MessengerMessageDispatcher:
        arguments:
            - '@eventBus'
        tags:
            - { name: andreo.eventsauce.messenger.message_dispatcher, bus: eventBus }
```

Add compiler pass to app kernel

```php

namespace App;

use Andreo\EventSauce\Messenger\DependencyInjection\HandleEventSauceMessageMiddlewarePass;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HandleEventSauceMessageMiddlewarePass(), priority: -10);
    }
}
```