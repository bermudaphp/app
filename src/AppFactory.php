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
        return php_sapi_name() == 'cli' ? new Console($container)
            : new FastCGI($container);
    }

    public static function make(ContainerInterface $container): AppInterface
    {
        $app = $container->has(AppInterface::class) ? $container->get(AppInterface::class)
            : (new self())($container);
        
        return Registry::set(AppInterface::class, $app);
    }
}
