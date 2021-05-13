<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;

/**
 * Class Bootstrapper
 * @package Bermuda\App\Boot
 */
final class Bootstrapper
{
    /**
     * @var BootstrapperInterface[]
     */
    private static array $bootstrap = [];
    private static bool $appIsBooted = false;

    /**
     * @param AppInterface $app
     */
    public static function boot(AppInterface $app): void
    {
        if (!self::$appIsBooted)
        {
            Registry::set(AppInterface::class, $app);
        
            foreach (self::$bootstrap as $bootstrapper)
            {
                $bootstrapper->boot($app);
            }
            
            self::$appIsBooted = true;
            
            return;
        }
        
        throw new \RuntimeException('App already booted.');
    }

    /**
     * @param BootstrapperInterface $bootstrapper
     * @return $this
     */
    public static function addBootstrapper(BootstrapperInterface $bootstrapper): self
    {
        self::$bootstrap[] = $bootstrapper;
        return $this;
    }

    /**
     * @param BootstrapperInterface[] $bootstrap
     * @return void
     */
    public static function addMany(iterable $bootstrap = []): void
    {
        foreach ($bootstrap as $bootstrapper)
        {
            self::addBootstrapper($bootstrapper);
        }
    }
}
