<?php

namespace Bermuda\App;

use Bermuda\Registry\Registry;
use Psr\Container\ContainerInterface;

final class AppFactory
{
    /**
     * @param ContainerInterface $container
     * @return Server|Console
     */
    public function __invoke(ContainerInterface $container): Server|Console
    {
        return is_console_sapi() ? Console::makeFrom($container)
            : Server::makeFrom($container);
    }
}
