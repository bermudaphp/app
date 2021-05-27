<?php

namespace Bermuda\App;

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
        ($app = $container->has(AppInterface::class) ? $container->get(AppInterface::class)
            : (php_sapi_name() == 'cli' ? new Console($container): new FastCGI($container)))
            ->boot();
        
        return Registry::set(AppInterface::class, $app);
    }
}
