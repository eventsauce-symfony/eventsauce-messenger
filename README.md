## eventsauce-messenger

Integration symfony messenger with the EventSauce

### Installation

```bash
composer require andreo/eventsauce-messenger
```

### Requirements

- PHP ^8.1
- Symfony messenger ^6.0

### Event dispatching

This dispatcher dispatch event only to handler that supports event type.
Doesn't dispatch headers

**Usage**

```php
use Andreo\EventSauce\Messenger\MessengerEventDispatcher;

new MessengerEventDispatcher(
    eventBus: $eventBus, // instance of Symfony\Component\Messenger\MessageBusInterface
);
```

**Handler example**

```php
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

final class FooProjectionHandler implements MessageSubscriberInterface
{
    public function onCreated(FooCreated $event)
    {
        // do something
    }

    public static function getHandledMessages(): iterable
    {
        yield FooCreated::class => [
            'method' => 'onCreated',
        ];
    }
}
```

### Event and headers dispatching

This dispatcher dispatch event only to handler that supports event type.
Receive of headers in the second handler argument

**Usage**

```php
use Andreo\EventSauce\Messenger\MessengerEventAndHeadersDispatcher;

new MessengerEventAndHeadersDispatcher(
    eventBus: $eventBus
);
```

**Handler example**

```php
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Andreo\EventSauce\Messenger\Headers;

final class FooProjectionHandler implements MessageSubscriberInterface
{
    public function onCreated(FooCreated $event, Headers $headers)
    {
        // do something
    }

    // ...
}
```

### Message dispatching

This dispatcher dispatch message to any handler that supports message type.
Message object includes the event and headers

**Usage**

```php
use Andreo\EventSauce\Messenger\MessengerEventDispatcher;

new MessengerEventDispatcher(
    eventBus: $eventBus
);
```

**Handler example**

```php
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Andreo\EventSauce\Messenger\Headers;
use EventSauce\EventSourcing\Message;

final class FooProjectionHandler implements MessageSubscriberInterface
{
    public function onCreated(Message $message)
    {
        if (!$message->event() instanceof FooCreated) {
            return;
        }
    }

    // ...
}
```

### Dependency Injection

If you want use **MessengerEventAndHeadersDispatcher**
you need to mark your event dispatcher with a dedicated tag

For example:

```yaml
    Andreo\EventSauce\Messenger\MessengerEventAndHeadersDispatcher:
        arguments:
            - '@messageBus'
        tags:
            - { name: andreo.event_sauce.event_and_headers_dispatcher, bus: messageBus }
```

and add dedicated compiler pass to your app kernel

```php

// src/Kernel.php
namespace App;

// ...
use Andreo\EventSauce\Messenger\DependencyInjection\HandleEventAndHeadersPass;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // ...

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HandleEventAndHeadersPass(), priority: -10);
    }
}

```