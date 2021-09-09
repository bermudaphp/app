<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\LogErrorListener;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

final class Bootstrapper implements BootstrapperInterface
{
    public function __construct(private ?iterable $bootstrap = [])
    {
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
        return array_merge($this->bootstrap, $bootstrap);
    }

    /**
     * @inerhitDoc
     */
    public static function makeDefault(ContainerInterface $container): self
    {
        return new self([
            new RouterBootstrapper(),
            new PipelineBootstrapper(),
            new ErrorHandlerBootstrapper([
                (new LogErrorListener($container->get(LoggerInterface::class)))
                    ->except('Bermuda\Router\Exception\RouteNotFoundException')
                    ->except('Bermuda\Router\Exception\MethodNotAllowedException')
            ])
        ]);
    }
}
