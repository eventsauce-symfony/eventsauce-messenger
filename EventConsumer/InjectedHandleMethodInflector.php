<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Messenger\EventConsumer;

use EventSauce\EventSourcing\EventConsumption\HandleMethodInflector;

trait InjectedHandleMethodInflector
{
    protected function handleMethodInflector(): HandleMethodInflector
    {
        return $this->handleMethodInflector;
    }
}
