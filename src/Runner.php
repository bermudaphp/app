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
  
    private function __construct(private ContainerInterface $container)
    {
    }
  
    public static function instantiate(ContainerInterface $container): self
    {
        return new self($container);
    }
  
    /**
     * @param AppInterface $app
     */
    public function run(): void
    {
        if (self::$app != null)
        {
            Registry::set(AppInterface::class, self::app = $this->container->get(AppInterface::class));
            self::$app->get(BootstrapperInterface::class)->boot(self::$app);
        }
        
        throw new \RuntimeException('App already runned.');
    }
}