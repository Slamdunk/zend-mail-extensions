<?php

declare(strict_types=1);

namespace Slam\Zend\Mail\Protocol;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

final class TimeKeeperSmtpProxyDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new TimeKeeperSmtpProxy(\call_user_func($callback));
    }
}
