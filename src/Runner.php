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
    {}
  
    public static function instantiate(ContainerInterface $container): self
    {
        return new self($container);
    }
  
    /**
     * Run application
     * @throws \RuntimeException
     */
    public function run(): void
    {
        if (self::$app != null)
        {
            throw new \RuntimeException('App already runned.');
        }
        
        Registry::set(AppInterface::class, self::$app = $this->container->get(AppInterface::class));
        self::$app->get(BootstrapperInterface::class)->boot(self::$app);
    }
}
