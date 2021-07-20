<?php

namespace Bermuda\App;

use Bermuda\Registry\Registry;
use Psr\Container\ContainerInterface;

use function Bermuda\{is_console_sapi, containerGet};

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
        return is_console_sapi() ? Console::makeFrom($container) : Server::makeFrom($container);
    }

    public static function make(ContainerInterface $container): AppInterface
    {
        return Registry::set(AppInterface::class, containerGet($container, AppInterface::class, (new AppFactory)($container)));
    }
}
