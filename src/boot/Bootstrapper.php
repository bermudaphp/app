<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;

/**
 * Class Bootstrapper
 * @package Bermuda\App\Boot
 */
final class Bootstrapper implements BootstrapperInterface
{
    /**
     * @var BootstrapperInterface[]
     */
    private array $bootstrap = [];

    /**
     * @param AppInterface $app
     */
    public function boot(AppInterface $app): void
    {
        foreach ($this->bootstrap as $bootstrapper) $bootstrapper->boot($app);
    }

    /**
     * @param BootstrapperInterface $bootstrapper
     * @return $this
     */
    public function add(BootstrapperInterface $bootstrapper): self
    {
        $this->bootstrap[] = $bootstrapper;
        return $this;
    }

    /**
     * @param BootstrapperInterface[] $bootstrap
     * @return self
     */
    public static function makeOf(iterable $bootstrap = []): self
    {
        $instance = new self();
        
        foreach ($bootstrap as $bootstrapper)
        {
            $instance->add($bootstrapper);
        }
        
        return $instance;
    }
}
