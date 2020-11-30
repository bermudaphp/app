<?php

namespace Bermuda\App;


use Bermuda\Registry\Registry;
use Psr\Container\ContainerInterface;


/**
 * Class AppFactory
 * @package Bermuda\App
 */
final class AppFactory
{
    /**
     * @param ContainerInterface $container
     * @return AppInterface
     */
    public function __invoke(ContainerInterface $container): AppInterface
    {
        return Registry::set(AppInterface::class, $this->make($container));
    }

    /**
     * @param ContainerInterface $container
     * @return AppInterface
     */
    private function make(ContainerInterface $container): AppInterface
    {
        if ($container->has(AppInterface::class))
        {
            return $container->get(AppInterface::class);
        }

        return php_sapi_name() == 'cli' ? new Console($container)
            : new FastCGI($container);
    }

    /**
     * @deprecated
     * @throws \RuntimeException
     */
    public function getEntries(): array
    {
        throw new \RuntimeException(__METHOD__ . ' is deprecated!');
    }
}
