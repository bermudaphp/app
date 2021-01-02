<?php

namespace Bermuda\App\Boot;


use Bermuda\App\AppInterface;


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
     * Bootstrapper constructor.
     * @param BootstrapperInterface[] $bootstrap
     */
    public function __construct(array $bootstrap = [])
    {
        foreach ($bootstrap as $bootstrapper)
        {
            $this->addBootstrapper($bootstrapper);
        }
    }

    /**
     * @param AppInterface $app
     */
    public function boot(AppInterface $app): void
    {
        Registry::set(AppInterface::class, $app);
        
        foreach ($this->bootstrap as $bootstrapper)
        {
            $bootstrapper->boot($app);
        }
    }

    /**
     * @param BootstrapperInterface $bootstrapper
     * @return $this
     */
    public function addBootstrapper(BootstrapperInterface $bootstrapper): self
    {
        $this->bootstrap[] = $bootstrapper;
        return $this;
    }

    /**
     * @param BootstrapperInterface[] $bootstrap
     * @return static
     */
    public static function makeOf(array $bootstrap = []): self
    {
        return new self($bootstrap);
    }
}
