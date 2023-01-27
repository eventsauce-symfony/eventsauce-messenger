## eventsauce-messenger 3.0

Integration symfony messenger for EventSauce

### Installation

```bash
composer require andreo/eventsauce-messenger
```

#### Previous versions doc

- [2.0](https://github.com/eventsauce-symfony/eventsauce-messenger/tree/2.0.0)

### Requirements

- PHP >=8.2
- Symfony messenger ^6.2

#### Event consumption

[See more about Event Consumers](https://eventsauce.io/docs/reacting-to-events/projections-and-read-models/)


Message handler example

```php

use Andreo\EventSauce\Messenger\EventConsumer\InjectedHandleMethodInflector;
use EventSauce\EventSourcing\EventConsumption\EventConsumer;
use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;
use Andreo\EventSauce\Messenger\Attribute\AsEventSauceMessageHandler;

final class FooBarBazMessageHandler extends EventConsumer
{
    // copy-paste trait for inject HandleMethodInflector of EventSauce
    // This example use EventSauce\EventSourcing\EventConsumption\InflectHandlerMethodsFromType. Remember, register your way
    use InjectedHandleMethodInflector;

    public function __construct(
        private HandleMethodInflector $handleMethodInflector
    )
    {}

    #[AsEventSauceMessageHandler(bus: 'eventBus')]
    public function onFooCreated(FooCreated $fooCreated, Message $message): void
    {
    }

    // You can define more handlers also union types(only with InflectHandlerMethodsFromType) if you want as below
    #[AsEventSauceMessageHandler(bus: 'eventBus')]
    public function onBarOrBazCreated(BarCreated|BazCreated $barCreated, Message $message): void
    {
    }
}
```

### Configuration

`AsEventSauceMessageHandler` attribute works with symfony autoconfigure feature.
If you want to use you need to register attribute.

```php

use Andreo\EventSauce\Messenger\DependencyInjection\RegisterEventSauceMessageHandlerAttribute;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        RegisterEventSauceMessageHandlerAttribute::register($container);
    }
}
```

If you don't want to use autoconfiguration, you can register the handlers manually.

```yaml
services:
  # ...
  App\Handler\FooBarBazMessageHandler:
    tags:
      -
        name: messenger.message_handler
        handles: App\Message\FooCreated
        bus: eventBus
        method: handle # must be set handle method of EventSauce EventConsumer
      -
        name: messenger.message_handler
        handles: App\Message\BarCreated
        bus: eventBus
        method: handle
      -
        name: messenger.message_handler
        handles: App\Message\BazCreated
        bus: eventBus
        method: handle

```

#### Rest configuration

Your services

```yaml
services:
  # ...
    Andreo\EventSauce\Messenger\Dispatcher\MessengerMessageDispatcher:
      arguments:
        $eventBus: 'eventBus' # bus alias from messenger config
        
    Andreo\EventSauce\Messenger\Middleware\HandleEventSauceMessageMiddleware:
      arguments:
        # change handlers locator prefix to your bus alias from messenger config
        $handlersLocator: '@eventBus.messenger.handlers_locator'
```

Messenger config

```yaml
framework:
  messenger:
    # ...
    buses:
      eventBus:
        # disable default config of messenger middleware 
        default_middleware: false
        # minimal middleware config. Note that there are other middleware you may want to use - check messenger docs
        middleware:
          - 'send_message'
          - 'Andreo\EventSauce\Messenger\Middleware\HandleEventSauceMessageMiddleware'
          - 'handle_message' # if you want to use default handling also, this middleware must be last set
```
