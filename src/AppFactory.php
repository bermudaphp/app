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
        $app = cget($container, AppInterface::class, static fn() => (new AppFactory)($container));
        cget($app, Boot\BootstrapperInterface::class, null)?->boot($app);
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
