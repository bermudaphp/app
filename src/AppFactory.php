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
}
