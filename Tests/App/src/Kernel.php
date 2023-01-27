<?php

namespace App;

use Andreo\EventSauce\Messenger\DependencyInjection\RegisterEventSauceMessageHandlerAttribute;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        RegisterEventSauceMessageHandlerAttribute::register($container);
    }
}
