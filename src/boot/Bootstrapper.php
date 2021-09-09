<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;

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
    public function addBootstrapper(BootstrapperInterface $bootstrapper): self
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
        $bootstrapper = new self;
        
        foreach ($bootstrap as $i)
        {
            $bootstrapper->addBootstrapper($i);
        }
        
        return $bootstrapper;
    }
}
