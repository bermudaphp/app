<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\LogErrorListener;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class Bootstrapper implements BootstrapperInterface
{
    private array $bootstrap = [];
    public function __construct(iterable $bootstrap = [])
    {
        foreach($bootstrap as $b) {
            $this->bootstrap[] = $b;
        }
    }

    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        foreach ($this->bootstrap as $bootstrapper) {
            if (!$bootstrapper instanceof BootstrapperInterface) {
                $bootstrapper = $app->get($bootstrapper);
            }

            $bootstrapper->boot($app);
        }
    }

    /**
     * @param BootstrapperInterface $bootstrapper
     * @return self
     */
    public function add(BootstrapperInterface $bootstrapper): self
    {
        $this->bootstrap[] = $bootstrapper;
        return $this;
    }

    /**
     * @param array $bootstrap
     * @return self
     */
    public function merge(array $bootstrap): self
    {
        $this->bootstrap = array_merge($this->bootstrap, $bootstrap);
    }

    /**
     * @inerhitDoc
     */
    public static function withDefaults(ContainerInterface $container): self
    {
        return new self([
            new RouterBootstrapper,
            new PipelineBootstrapper,
            new ErrorHandlerBootstrapper(
                $container->get('config')['errors']['listeners'] ?? []
            ),
            new HttpBootstrapper,
        ]);
    }
}
