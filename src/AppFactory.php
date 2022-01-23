<?php

namespace Bermuda\App;

use Bermuda\Registry\Registry;
use Psr\Container\ContainerInterface;
use function Bermuda\Config\cget;

final class AppFactory
{
    /**
     * @param ContainerInterface $container
     * @return AppInterface
     */
    public static function create(ContainerInterface $container): AppInterface
    {
        $app = $container->get(AppInterface::class);
        $app->get(Boot\BootstrapperInterface::class)->boot($app);
     
        return Registry::set(AppInterface::class, $app);
    }

    /**
     * @param ContainerInterface $container
     * @return AppInterface
     */
    public function __invoke(ContainerInterface $container): AppInterface
    {
        return is_console_sapi() ? Console::makeFrom($container)
            : Server::makeFrom($container);
    }
}
