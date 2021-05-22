<?php

namespace Bermuda\App;

use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;
use Bermuda\App\Boot\BootstrapperInterface;
use Psr\Container\ContainerInterface;

/**
 * Class Runner
 * @package Bermuda\App
 */
final class Runner
{
    private static ?AppInterface $app = null;
    
    /**
     * Run application
     * @throws \RuntimeException
     */
    public static function run(ContainerInterface $container): void
    {
        if (self::$app != null)
        {
            throw new \RuntimeException('App already runned.');
        }

        Registry::set(AppInterface::class, self::$app = $container->get(AppInterface::class));
        self::$app->get(BootstrapperInterface::class)->boot(self::$app);
    }
}
